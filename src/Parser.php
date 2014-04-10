<?php
namespace Pharborist;

/**
 * Parses PHP tokens into syntax tree.
 */
class Parser {
  /**
   * @var array
   */
  private static $namespacePathTypes = [T_STRING, T_NS_SEPARATOR, T_NAMESPACE];

  /**
   * @var array
   */
  private static $visibilityTypes = [T_PUBLIC, T_PROTECTED, T_PRIVATE];

  /**
   * Iterator over PHP tokens.
   * @var TokenIterator
   */
  private $iterator;

  /**
   * Skipped hidden tokens.
   * @var TokenNode[]
   */
  private $skipped = [];

  /**
   * Skipped document comment.
   * @var DocCommentNode
   */
  private $docComment = NULL;

  /**
   * Skipped hidden tokens after document comment.
   */
  private $skippedDocComment = [];

  /**
   * The root node of the syntax tree.
   * @var Node
   */
  private $top;

  /**
   * Parser used for parsing expressions.
   * @var ExpressionParser
   */
  private $expressionParser;

  /**
   * @var TokenNode
   */
  private $current;

  /**
   * @var int
   */
  private $currentType;

  /**
   * Constructor.
   */
  public function __construct() {
    // Define future PHP constants if not already defined.
    if (!defined('T_FINALLY')) {
      define('T_FINALLY', 'finally');
    }
    if (!defined('T_YIELD')) {
      define('T_YIELD', 'yield');
    }
    if (!defined('T_ELLIPSIS')) {
      define('T_ELLIPSIS', '...');
    }
    if (!defined('T_POW')) {
      define('T_POW', '**');
    }
    if (!defined('T_POW_EQUAL')) {
      define('T_POW_EQUAL', '**=');
    }
    $this->expressionParser = new ExpressionParser();
  }

  /**
   * Build a syntax tree from the token iterator.
   * @param TokenIterator $iterator
   * @return StatementBlockNode Root node of the tree
   */
  public function buildTree(TokenIterator $iterator) {
    $this->iterator = $iterator;
    $this->current = $this->iterator->current();
    $this->currentType = $this->current ? $this->current->getType() : NULL;
    $top = new StatementBlockNode();
    $this->top = $top;
    // Parse any template statements that proceed the opening PHP tag.
    $this->templateStatementList($top);
    if ($this->tryMatch(T_OPEN_TAG, $top, NULL, TRUE)) {
      $this->topStatementList($top);
    }
    return $top;
  }

  /**
   * Parse a file and return the parsed tree
   * @param string $filename Path to file
   * @return ParentNode|bool
   *   The top-level node of the parsed tree or FALSE if the file contents
   *   could not be read.
   */
  public static function parseFile($filename) {
    if ($source = @file_get_contents($filename)) {
      return self::parseSource($source);
    }
    return FALSE;
  }

  /**
   * Parse PHP source code and return the parsed tree.
   * @param string $source PHP source code
   * @return ParentNode
   *   The top-level node of the parsed tree
   */
  public static function parseSource($source) {
    static $tokenizer, $parser = NULL;
    if (!isset($parser)) {
      $tokenizer = new Tokenizer();
      $parser = new self();
    }
    $tokens = $tokenizer->getAll($source);
    return $parser->buildTree(new TokenIterator($tokens));
  }

  /**
   * Parse a snippet of PHP and return the parsed tree.
   * @param string $snippet PHP snippet without the opening PHP tag
   * @return ParentNode
   *   The top-level node of the parsed tree
   */
  public static function parseSnippet($snippet) {
    $tree = self::parseSource('<?php ' . $snippet);
    // Strip the inserted opening php tag
    $tree->removeFirst();
    return $tree;
  }

  /**
   * Parse zero or more template statements.
   * @param ParentNode $node Node to append matches to.
   * @throws ParserException
   */
  private function templateStatementList(ParentNode $node) {
    while ($this->current) {
      if ($this->currentType === T_OPEN_TAG) {
        return;
      }
      elseif ($this->currentType === T_INLINE_HTML) {
        $node->appendChild($this->mustMatchToken(T_INLINE_HTML));
      }
      elseif ($this->currentType === T_OPEN_TAG_WITH_ECHO) {
        $node->appendChild($this->echoTagStatement());
      }
      else {
        throw new ParserException($this->iterator->getSourcePosition(),
          'expected PHP opening tag, but got ' . $this->iterator->current()->getText());
      }
    }
  }

  /**
   * Parse an echo PHP (eg. <?=$a?>) statement.
   * @return EchoTagStatementNode
   */
  private function echoTagStatement() {
    $node = new EchoTagStatementNode();
    $this->mustMatch(T_OPEN_TAG_WITH_ECHO, $node);
    $node->appendChild($this->exprList());
    $this->mustMatch(T_CLOSE_TAG, $node);
    return $node;
  }

  /**
   * Parse a list of top level statements.
   * @param StatementBlockNode $node Node to append matches to
   * @param string $terminator Character that ends the statement list
   */
  private function topStatementList(StatementBlockNode $node, $terminator = '') {
    $this->matchHidden($node);
    while ($this->currentType !== NULL && $this->currentType !== $terminator) {
      $node->appendChild($this->topStatement(), 'statements');
      $this->matchHidden($node);
    }
    $this->matchHidden($node);
  }

  /**
   * Parse a block of top level statements.
   * @param string $terminator Character that ends the statement block
   * @return Node
   */
  private function topStatementBlock($terminator) {
    $node = new StatementBlockNode();
    $this->topStatementList($node, $terminator);
    return $node;
  }

  /**
   * Parse a top level statement.
   * @return Node
   */
  private function topStatement() {
    switch ($this->currentType) {
      case T_USE:
        return $this->_use();
      case T_CONST:
        return $this->_const();
      case T_ABSTRACT:
      case T_FINAL:
      case T_CLASS:
        return $this->classDeclaration();
      case T_INTERFACE:
        return $this->interfaceDeclaration();
      case T_TRAIT:
        return $this->traitDeclaration();
      case T_HALT_COMPILER:
        $node = new HaltCompilerNode();
        $this->mustMatch(T_HALT_COMPILER, $node, 'namespacePath');
        $this->mustMatch('(', $node);
        $this->mustMatch(')', $node);
        $this->mustMatch(';', $node, NULL, TRUE);
        return $node;
      default:
        if ($this->currentType === T_FUNCTION && $this->isLookAhead(T_STRING, '&')) {
          return $this->functionDeclaration();
        }
        elseif ($this->currentType === T_NAMESPACE && !$this->isLookAhead(T_NS_SEPARATOR)) {
          return $this->_namespace();
        }
        return $this->statement();
    }
  }

  /**
   * Parse a constant declaration list.
   * @return ConstantDeclarationStatementNode
   */
  private function _const() {
    $node = new ConstantDeclarationStatementNode();
    $this->matchDocComment($node);
    $this->mustMatch(T_CONST, $node);
    do {
      $node->appendChild($this->constDeclaration(), 'declarations');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a constant declaration.
   * @return ConstantDeclarationNode
   */
  private function constDeclaration() {
    $node = new ConstantDeclarationNode();
    $this->mustMatch(T_STRING, $node, 'name', TRUE);
    if ($this->mustMatch('=', $node)) {
      $node->appendChild($this->staticScalar(), 'value');
    }
    return $node;
  }

  /**
   * Parse a statement.
   * @return Node
   */
  private function statement() {
    switch ($this->currentType) {
      case T_CLOSE_TAG:
        // A close tag escapes into template mode.
        $node = new TemplateNode();
        $this->mustMatch(T_CLOSE_TAG, $node);
        $this->templateStatementList($node);
        if ($this->iterator->hasNext()) {
          $this->mustMatch(T_OPEN_TAG, $node);
        }
        return $node;
      case T_IF:
        return $this->_if();
      case T_WHILE:
        return $this->_while();
      case T_DO:
        return $this->doWhile();
      case T_FOR:
        return $this->_for();
      case T_SWITCH:
        return $this->_switch();
      case T_BREAK:
        return $this->_break();
      case T_CONTINUE:
        return $this->_continue();
      case T_RETURN:
        return $this->_return();
      case T_YIELD:
        $node = new YieldStatementNode();
        $node->appendChild($this->_yield());
        $this->mustMatch(';', $node, NULL, TRUE);
        return $node;
      case T_GLOBAL:
        return $this->_global();
      case T_ECHO:
        return $this->_echo();
      case T_UNSET:
        return $this->_unset();
      case T_FOREACH:
        return $this->_foreach();
      case T_DECLARE:
        return $this->_declare();
      case T_TRY:
        return $this->_try();
      case T_THROW:
        return $this->_throw();
      case T_GOTO:
        return $this->_goto();
      case '{':
        return $this->innerStatementBlock();
      case ';':
        return $this->mustMatchToken(';');
      case T_STATIC:
        if ($this->isLookAhead(T_VARIABLE)) {
          return $this->staticVariableList();
        }
        else {
          return $this->exprStatement();
        }
      case T_STRING:
        if ($this->isLookAhead(':')) {
          $node = new GotoLabelNode();
          $this->mustMatch(T_STRING, $node);
          $this->mustMatch(':', $node, NULL, TRUE);
          return $node;
        }
        else {
          return $this->exprStatement();
        }
      default:
        return $this->exprStatement();
    }
  }

  /**
   * Parse a static variable list.
   * @return StaticVariableStatementNode
   */
  private function staticVariableList() {
    $node = new StaticVariableStatementNode();
    $this->mustMatch(T_STATIC, $node);
    do {
      $node->appendChild($this->staticVariable(), 'variables');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a static variable.
   * @return StaticVariableNode
   */
  private function staticVariable() {
    $node = new StaticVariableNode();
    $this->mustMatch(T_VARIABLE, $node, 'name');
    if ($this->tryMatch('=', $node)) {
      $node->appendChild($this->staticScalar(), 'initialValue');
    }
    return $node;
  }

  /**
   * Parse expression statement.
   * @return ExpressionStatementNode
   */
  private function exprStatement() {
    $node = new ExpressionStatementNode();
    $this->matchDocComment($node);
    $node->appendChild($this->expr());
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse parenthesis expression.
   * @return Node
   */
  private function parenExpr() {
    $node = new ParenthesisNode();
    $this->mustMatch('(', $node);
    if ($this->currentType === T_YIELD) {
      $node->appendChild($this->_yield());
    }
    else {
      $node->appendChild($this->expr());
    }
    $this->mustMatch(')', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse if control structure.
   * @return IfNode
   */
  private function _if() {
    $node = new IfNode();
    $this->mustMatch(T_IF, $node);
    $node->appendChild($this->parenExpr(), 'condition');
    if ($this->tryMatch(':', $node)) {
      $node->appendChild($this->innerIfInnerStatementList(), 'then');
      while ($this->currentType === T_ELSEIF) {
        $elseIf = new ElseIfNode();
        $this->mustMatch(T_ELSEIF, $elseIf);
        $elseIf->appendChild($this->parenExpr(), 'condition');
        $this->mustMatch(':', $elseIf);
        $elseIf->appendChild($this->innerIfInnerStatementList(), 'then');
        $node->appendChild($elseIf, 'elseIfList');
      }
      if ($this->tryMatch(T_ELSE, $node)) {
        $this->mustMatch(':', $node);
        $node->appendChild($this->innerStatementListNode(T_ENDIF), 'else');
      }
      $this->mustMatch(T_ENDIF, $node);
      $this->mustMatch(';', $node, NULL, TRUE);
      return $node;
    }
    else {
      $this->matchHidden($node);
      $node->appendChild($this->statement());
      while ($this->currentType === T_ELSEIF) {
        $elseIf = new ElseIfNode();
        $this->mustMatch(T_ELSEIF, $elseIf);
        $elseIf->appendChild($this->parenExpr(), 'condition');
        $this->matchHidden($elseIf);
        $elseIf->appendChild($this->statement(), 'then');
        $node->appendChild($elseIf, 'elseIfList');
      }
      if ($this->tryMatch(T_ELSE, $node)) {
        $node->appendChild($this->statement(), 'else');
      }
      return $node;
    }
  }

  /**
   * Parse statements for alternative if syntax.
   * @return Node
   */
  private function innerIfInnerStatementList() {
    static $terminators = [T_ELSEIF, T_ELSE, T_ENDIF];
    $node = new StatementBlockNode();
    $this->matchHidden($node);
    while ($this->currentType !== NULL && !in_array($this->currentType, $terminators)) {
      $node->appendChild($this->innerStatement(), 'statements');
      $this->matchHidden($node);
    }
    return $node;
  }

  /**
   * Parse while control structure.
   * @return WhileNode
   */
  private function _while() {
    $node = new WhileNode();
    $this->mustMatch(T_WHILE, $node);
    $node->appendChild($this->parenExpr(), 'condition');
    if ($this->tryMatch(':', $node)) {
      $node->appendChild($this->innerStatementListNode(T_ENDWHILE), 'body');
      $this->mustMatch(T_ENDWHILE, $node);
      $this->mustMatch(';', $node, NULL, TRUE);
      return $node;
    }
    else {
      $this->matchHidden($node);
      $node->appendChild($this->statement(), 'body');
      return $node;
    }
  }

  /**
   * Parse do while control stucture.
   * @return DoWhileNode
   */
  private function doWhile() {
    $node = new DoWhileNode();
    $this->mustMatch(T_DO, $node);
    $node->appendChild($this->statement(), 'body');
    $this->mustMatch(T_WHILE, $node);
    $node->appendChild($this->parenExpr(), 'condition');
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse for control structure.
   * @return ForNode
   */
  private function _for() {
    $node = new ForNode();
    $this->mustMatch(T_FOR, $node);
    $this->mustMatch('(', $node);
    $this->forExpr($node, ';', 'initial');
    $this->forExpr($node, ';', 'condition');
    $this->forExpr($node, ')', 'step', TRUE);
    if ($this->tryMatch(':', $node)) {
      $node->appendChild($this->innerStatementListNode(T_ENDFOR), 'body');
      $this->mustMatch(T_ENDFOR, $node);
      $this->mustMatch(';', $node, NULL, TRUE);
      return $node;
    }
    else {
      $this->matchHidden($node);
      $node->appendChild($this->statement(), 'body');
      return $node;
    }
  }

  /**
   * Parse a for expression.
   * @param ForNode $parent Parent for node
   * @param int|string $terminator Token type that terminates the for expression
   * @param string $property_name
   * @param bool $is_last TRUE if last for expression
   */
  private function forExpr(ForNode $parent, $terminator, $property_name, $is_last = FALSE) {
    if ($this->tryMatch($terminator, $parent)) {
      return;
    }
    $parent->appendChild($this->exprList(), $property_name);
    $this->mustMatch($terminator, $parent, NULL, $is_last);
  }

  /**
   * Parse a switch control structure.
   * @return SwitchNode
   */
  private function _switch() {
    $node = new SwitchNode();
    $this->mustMatch(T_SWITCH, $node);
    $node->appendChild($this->parenExpr(), 'switchOn');
    if ($this->tryMatch(':', $node)) {
      $this->tryMatch(';', $node);
      while ($this->currentType !== NULL && $this->currentType !== T_ENDSWITCH) {
        $node->appendChild($this->caseStatement(T_ENDSWITCH), 'cases');
      }
      $this->mustMatch(T_ENDSWITCH, $node);
      $this->mustMatch(';', $node, NULL, TRUE);
      return $node;
    }
    else {
      $this->mustMatch('{', $node);
      $this->tryMatch(';', $node);
      while ($this->currentType !== NULL && $this->currentType !== '}') {
        $node->appendChild($this->caseStatement('}'), 'cases');
      }
      $this->mustMatch('}', $node, NULL, TRUE);
      return $node;
    }
  }

  /**
   * Parse a case statement.
   * @param int|string $terminator Token type that terminates statement list
   * @return CaseNode|DefaultNode
   * @throws ParserException
   */
  private function caseStatement($terminator) {
    if ($this->currentType === T_CASE) {
      $node = new CaseNode();
      $this->mustMatch(T_CASE, $node);
      $node->appendChild($this->expr(), 'matchOn');
      if (!$this->tryMatch(':', $node, NULL, TRUE) && !$this->tryMatch(';', $node, NULL, TRUE)) {
        throw new ParserException($this->iterator->getSourcePosition(), 'expected :');
      }
      $node->appendChild($this->innerCaseStatementList($terminator), 'body');
      return $node;
    }
    elseif ($this->currentType === T_DEFAULT) {
      $node = new DefaultNode();
      $this->mustMatch(T_DEFAULT, $node);
      if (!$this->tryMatch(':', $node, NULL, TRUE) && !$this->tryMatch(';', $node, NULL, TRUE)) {
        throw new ParserException($this->iterator->getSourcePosition(), 'expected :');
      }
      $node->appendChild($this->innerCaseStatementList($terminator), 'body');
      return $node;
    }
    throw new ParserException($this->iterator->getSourcePosition(), "expected case or default");
  }

  /**
   * Parse the inner statements for a case statement.
   * @param int|string $terminator Token type that terminates statement list
   * @return Node
   */
  private function innerCaseStatementList($terminator) {
    static $terminators = [T_CASE, T_DEFAULT];
    $node = new StatementBlockNode();
    $this->matchHidden($node);
    while ($this->currentType !== NULL && $this->currentType !== $terminator && !in_array($this->currentType, $terminators)) {
      $node->appendChild($this->innerStatement(), 'statements');
      $this->matchHidden($node);
    }
    return $node;
  }

  /**
   * Parse a break statement.
   * @return BreakStatementNode
   */
  private function _break() {
    $node = new BreakStatementNode();
    $this->mustMatch(T_BREAK, $node);
    if ($this->tryMatch(';', $node, NULL, TRUE)) {
      return $node;
    }
    $this->parseLevel($node);
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a continue statement.
   * @return ContinueStatementNode
   */
  private function _continue() {
    $node = new ContinueStatementNode();
    $this->mustMatch(T_CONTINUE, $node);
    if ($this->tryMatch(';', $node, NULL, TRUE)) {
      return $node;
    }
    $this->parseLevel($node);
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a break/continue level.
   * @param BreakStatementNode|ContinueStatementNode $node
   */
  private function parseLevel($node) {
    if ($this->tryMatch('(', $node)) {
      $this->mustMatch(T_LNUMBER, $node, 'level');
      $this->mustMatch(')', $node);
    }
    else {
      $this->mustMatch(T_LNUMBER, $node, 'level');
    }
  }

  /**
   * Parse a return statement.
   * @return ReturnStatementNode
   */
  private function _return() {
    $node = new ReturnStatementNode();
    $this->mustMatch(T_RETURN, $node);
    if ($this->tryMatch(';', $node, NULL, TRUE)) {
      return $node;
    }
    $node->appendChild($this->expr(), 'value');
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a yield expression.
   * @return YieldNode
   */
  private function _yield() {
    $node = new YieldNode();
    $this->mustMatch(T_YIELD, $node);
    $expr = $this->expr();
    if ($this->tryMatch(T_DOUBLE_ARROW, $node)) {
      $node->appendChild($expr, 'key');
      $node->appendChild($this->expr(), 'value');
    }
    else {
      $node->appendChild($expr, 'value');
    }
    return $node;
  }

  /**
   * Parse a global variable declaration list.
   * @return GlobalStatementNode
   */
  private function _global() {
    $node = new GlobalStatementNode();
    $this->mustMatch(T_GLOBAL, $node);
    do {
      $node->appendChild($this->globalVar(), 'variables');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse global variable.
   * @return Node
   * @throws ParserException
   */
  private function globalVar() {
    if ($this->currentType === T_VARIABLE) {
      return $this->mustMatchToken(T_VARIABLE);
    }
    elseif ($this->currentType === '$') {
      if ($this->isLookAhead('{')) {
        return $this->_compoundVariable();
      }
      else {
        $node = new VariableVariableNode();
        $this->mustMatch('$', $node);
        $node->appendChild($this->variable());
        return $node;
      }
    }
    throw new ParserException($this->iterator->getSourcePosition(), 'expected a global variable (eg. T_VARIABLE)');
  }

  /**
   * Parse echo statement.
   * @return EchoStatementNode
   */
  private function _echo() {
    $node = new EchoStatementNode();
    $this->mustMatch(T_ECHO, $node);
    do {
      $node->appendChild($this->expr(), 'expressions');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse an unset statement.
   * @return UnsetStatementNode
   */
  private function _unset() {
    $statement_node = new UnsetStatementNode();
    $node = new UnsetNode();
    $this->mustMatch(T_UNSET, $node, 'namespacePath');
    $this->mustMatch('(', $node);
    do {
      $node->appendChild($this->variable(), 'arguments');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(')', $node);
    $statement_node->appendChild($node, 'functionCall');
    $this->mustMatch(';', $statement_node, NULL, TRUE);
    return $statement_node;
  }

  /**
   * Parse foreach control structure.
   * @return ForeachNode
   */
  private function _foreach() {
    $node = new ForeachNode();
    $this->mustMatch(T_FOREACH, $node);
    $this->mustMatch('(', $node);
    $node->appendChild($this->expr(), 'onEach');
    $this->mustMatch(T_AS, $node);
    $value = $this->foreachVariable();
    if ($this->currentType === T_DOUBLE_ARROW) {
      $node->appendChild($value, 'value');
      $this->mustMatch(T_DOUBLE_ARROW, $node);
      $node->appendChild($this->foreachVariable(), 'value');
    }
    else {
      $node->appendChild($value, 'value');
    }
    $this->mustMatch(')', $node);
    if ($this->tryMatch(':', $node)) {
      $node->appendChild($this->innerStatementListNode(T_ENDFOREACH), 'body');
      $this->mustMatch(T_ENDFOREACH, $node);
      $this->mustMatch(';', $node, NULL, TRUE);
      return $node;
    }
    else {
      $node->appendChild($this->statement(), 'body');
      return $node;
    }
  }

  /**
   * Parse a foreach variable.
   * @return Node
   */
  private function foreachVariable() {
    if ($this->currentType === T_LIST) {
      return $this->_list();
    }
    else {
      if ($this->currentType === '&') {
        return $this->writeVariable();
      }
      else {
        return $this->variable();
      }
    }
  }

  /**
   * Parse a list() expression.
   * @return ListNode
   */
  private function _list() {
    $node = new ListNode();
    $this->mustMatch(T_LIST, $node, 'functionReference');
    $this->mustMatch('(', $node);
    do {
      if ($this->tryMatch(')', $node, NULL, TRUE)) {
        return $node;
      }
      if ($this->currentType !== ',') {
        $node->appendChild($this->listElement(), 'arguments');
      }
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(')', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse an element from list() expression.
   * @return Node
   */
  private function listElement() {
    if ($this->currentType === T_LIST) {
      return $this->_list();
    }
    else {
      return $this->variable();
    }
  }

  /**
   * Parse a declare statement.
   * @return DeclareNode
   */
  private function _declare() {
    $node = new DeclareNode();
    $this->mustMatch(T_DECLARE, $node);
    $this->mustMatch('(', $node);
    if (!$this->tryMatch(')', $node)) {
      do {
        $declare_directive = new DeclareDirectiveNode();
        $this->tryMatch(T_STRING, $declare_directive, 'name');
        if ($this->tryMatch('=', $declare_directive)) {
          $declare_directive->appendChild($this->staticScalar(), 'value');
        }
        $node->appendChild($declare_directive, 'directives');
      } while ($this->tryMatch(',', $node));
      $this->mustMatch(')', $node);
    }
    if ($this->tryMatch(':', $node)) {
      $node->appendChild($this->innerStatementListNode(T_ENDDECLARE), 'body');
      $this->mustMatch(T_ENDDECLARE, $node);
      $this->mustMatch(';', $node, NULL, TRUE);
      return $node;
    }
    else {
      $node->appendChild($this->statement(), 'body');
      return $node;
    }
  }

  /**
   * Parse a try control structure.
   * @return TryCatchNode
   */
  private function _try() {
    $node = new TryCatchNode();
    $this->mustMatch(T_TRY, $node);
    $node->appendChild($this->innerStatementBlock(), 'try');
    $catch_node = new CatchNode();
    while ($this->tryMatch(T_CATCH, $catch_node)) {
      $this->mustMatch('(', $catch_node);
      $catch_node->appendChild($this->namespacePath(), 'exceptionType');
      $this->mustMatch(T_VARIABLE, $catch_node, 'variable');
      $this->mustMatch(')', $catch_node);
      $catch_node->appendChild($this->innerStatementBlock(), 'body');
      $node->appendChild($catch_node, 'catches');
      $catch_node = new CatchNode();
    }
    if ($this->tryMatch(T_FINALLY, $node)) {
      $node->appendChild($this->innerStatementBlock(), 'finally');
    }
    return $node;
  }

  /**
   * Parse a throw statement.
   * @return ThrowStatementNode
   */
  private function _throw() {
    $node = new ThrowStatementNode();
    $this->mustMatch(T_THROW, $node);
    $node->appendChild($this->expr());
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a goto statement.
   * @return GotoStatementNode
   */
  private function _goto() {
    $node = new GotoStatementNode();
    $this->mustMatch(T_GOTO, $node);
    $this->mustMatch(T_STRING, $node, 'label');
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a list of expressions.
   * @return ExpressionListNode
   */
  private function exprList() {
    $node = new ExpressionListNode();
    do {
      $node->appendChild($this->expr(), 'expressions');
    } while ($this->tryMatch(',', $node));
    return $node;
  }

  /**
   * Parse a static scalar expression.
   * @return Node
   * @throws ParserException
   */
  private function staticScalar() {
    if ($this->currentType === T_ARRAY) {
      $node = new ArrayNode();
      $this->mustMatch(T_ARRAY, $node);
      $this->mustMatch('(', $node);
      $this->staticArrayPairList($node, ')');
      $this->mustMatch(')', $node, NULL, TRUE);
      return $node;
    }
    elseif ($this->currentType === '[') {
      $node = new ArrayNode();
      $this->mustMatch('[', $node);
      $this->staticArrayPairList($node, ']');
      $this->mustMatch(']', $node, NULL, TRUE);
      return $node;
    }
    else {
      return $this->expr(TRUE);
    }
  }

  /**
   * Parse static operand.
   * @return Node
   */
  private function staticOperand() {
    static $scalar_types = [
      T_STRING_VARNAME,
      T_CLASS_C,
      T_LNUMBER,
      T_DNUMBER,
      T_CONSTANT_ENCAPSED_STRING,
      T_LINE,
      T_FILE,
      T_DIR,
      T_TRAIT_C,
      T_METHOD_C,
      T_FUNC_C,
      T_NS_C,
    ];
    if ($scalar = $this->tryMatchToken($scalar_types)) {
      return $scalar;
    }
    elseif ($this->currentType === '(') {
      $node = new ParenthesisNode();
      $this->mustMatch('(', $node);
      $node->appendChild($this->staticScalar());
      $this->mustMatch(')', $node, NULL, TRUE);
      return $node;
    }
    elseif (in_array($this->currentType, self::$namespacePathTypes)) {
      $namespace_path = $this->namespacePath();
      if ($this->currentType === T_DOUBLE_COLON) {
        $colon_node = new PartialNode();
        $this->mustMatch(T_DOUBLE_COLON, $colon_node);
        if ($this->currentType === T_CLASS) {
          return $this->classNameScalar($namespace_path, $colon_node);
        }
        else {
          $class_constant = $this->mustMatchToken(T_STRING);
          return $this->classConstant($namespace_path, $colon_node, $class_constant);
        }
      }
      else {
        return $namespace_path;
      }
    }
    elseif ($this->currentType === T_STATIC) {
      $static_node = $this->mustMatchToken(T_STATIC);
      $colon_node = new PartialNode();
      $this->mustMatch(T_DOUBLE_COLON, $colon_node);
      if ($this->currentType === T_CLASS) {
        return $this->classNameScalar($static_node, $colon_node);
      }
      else {
        $class_constant = $this->mustMatchToken(T_STRING);
        return $this->classConstant($static_node, $colon_node, $class_constant);
      }
    }
    elseif ($this->currentType === T_START_HEREDOC) {
      $node = new HeredocNode();
      $this->mustMatch(T_START_HEREDOC, $node);
      if ($this->tryMatch(T_END_HEREDOC, $node)) {
        return $node;
      }
      $this->mustMatch(T_ENCAPSED_AND_WHITESPACE, $node);
      $this->mustMatch(T_END_HEREDOC, $node);
      return $node;
    }
    else {
      return NULL;
    }
  }

  /**
   * Parse an expression.
   * @param bool $static TRUE if static expression
   * @return Node
   * @throws ParserException
   */
  private function expr($static = FALSE) {
    static $end_expression_types = [':', ';', ',', ')', ']', '}', T_AS, T_DOUBLE_ARROW];
    // Group tokens into operands & operators to pass to the expression parser
    $expression_nodes = array();
    while ($this->currentType !== NULL && !in_array($this->currentType, $end_expression_types)) {
      if ($op = $this->exprOperator($static)) {
        $expression_nodes[] = $op;
        if ($op->type === T_INSTANCEOF) {
          $expression_nodes[] = $this->classNameReference();
        }
      }
      elseif ($operand = ($static ? $this->staticOperand() : $this->exprOperand())) {
        $expression_nodes[] = $operand;
      }
      else {
        throw new ParserException($this->iterator->getSourcePosition(), "invalid expression");
      }
    }
    return $this->expressionParser->parse($expression_nodes);
  }

  /**
   * Parse an expression operator.
   * @param bool $static Static operator
   * @return Operator
   */
  private function exprOperator($static = FALSE) {
    $token_type = $this->currentType;
    if ($operator = OperatorFactory::createOperator($token_type, $static)) {
      $this->mustMatch($token_type, $operator, 'operator');
      if ($token_type === '?') {
        if ($this->currentType === ':') {
          $colon = new PartialNode();
          $this->mustMatch(':', $colon);
          return OperatorFactory::createElvisOperator($operator, $colon);
        }
        else {
          $operator->then = $static ? $this->staticScalar() : $this->expr();
          $colon = new PartialNode();
          $this->mustMatch(':', $colon);
          $operator->colon = $colon;
          return $operator;
        }
      }
      elseif ($token_type === '=' && $this->currentType === '&') {
        $by_ref_node = new PartialNode();
        $this->mustMatch('&', $by_ref_node);
        return OperatorFactory::createAssignReferenceOperator($operator, $by_ref_node);
      }
      return $operator;
    }
    return NULL;
  }

  /**
   * Parse an expression operand.
   * @return Node
   * @throws ParserException
   */
  private function exprOperand() {
    switch ($this->currentType) {
      case T_STRING_VARNAME:
      case T_CLASS_C:
      case T_LNUMBER:
      case T_DNUMBER:
      case T_LINE:
      case T_FILE:
      case T_DIR:
      case T_TRAIT_C:
      case T_METHOD_C:
      case T_FUNC_C:
      case T_NS_C:
      case T_YIELD:
        return $this->mustMatchToken($this->currentType);
      case T_CONSTANT_ENCAPSED_STRING:
        return $this->arrayDeference($this->mustMatchToken(T_CONSTANT_ENCAPSED_STRING));
      case T_ARRAY:
        $node = new ArrayNode();
        $this->mustMatch(T_ARRAY, $node);
        $this->mustMatch('(', $node);
        $this->arrayPairList($node, ')');
        $this->mustMatch(')', $node);
        return $this->arrayDeference($node);
      case '[':
        $node = new ArrayNode();
        $this->mustMatch('[', $node);
        $this->arrayPairList($node, ']');
        $this->mustMatch(']', $node);
        return $this->arrayDeference($node);
      case '(':
        $node = new ParenthesisNode();
        $this->mustMatch('(', $node);
        if ($this->currentType === T_NEW) {
          $node->appendChild($this->newExpr());
          $this->mustMatch(')', $node, NULL, TRUE);
          $node = $this->objectDereference($this->arrayDeference($node));
        }
        elseif ($this->currentType === T_YIELD) {
          $node->appendChild($this->_yield());
          $this->mustMatch(')', $node, NULL, TRUE);
        }
        else {
          $node->appendChild($this->expr());
          $this->mustMatch(')', $node, NULL, TRUE);
        }
        return $node;
      case T_START_HEREDOC:
        $node = new HeredocNode();
        $this->mustMatch(T_START_HEREDOC, $node);
        if ($this->tryMatch(T_END_HEREDOC, $node, NULL, TRUE)) {
          return $node;
        }
        else {
          $this->encapsList($node, T_END_HEREDOC, TRUE);
          $this->mustMatch(T_END_HEREDOC, $node, NULL, TRUE);
          return $node;
        }
      case '"':
        $node = new ComplexStringNode();
        $this->mustMatch('"', $node);
        $this->encapsList($node, '"');
        $this->mustMatch('"', $node);
        return $node;
      case T_STRING:
      case T_NS_SEPARATOR:
      case T_NAMESPACE:
        $namespace_path = $this->namespacePath();
        if ($this->currentType === T_DOUBLE_COLON) {
          return $this->exprClass($namespace_path);
        }
        elseif ($this->currentType === '(') {
          return $this->functionCall($namespace_path);
        }
        else {
          return $namespace_path;
        }
      case T_STATIC:
        $static = $this->mustMatchToken(T_STATIC);
        if ($this->currentType === T_FUNCTION) {
          return $this->anonymousFunction($static);
        } else {
          return $this->exprClass($static);
        }
      case '$':
      case T_VARIABLE:
        $operand = $this->indirectReference();
        if (!($operand instanceof VariableVariableNode) && $this->currentType === T_DOUBLE_COLON) {
          return $this->exprClass($operand);
        }
        elseif ($this->currentType === '(') {
          return $this->functionCall($operand);
        }
        else {
          return $this->objectDereference($operand);
        }
      case T_ISSET:
        $node = new IssetNode();
        $this->mustMatch(T_ISSET, $node, 'functionReference');
        $this->mustMatch('(', $node);
        do {
          $node->appendChild($this->expr(), 'arguments');
        } while ($this->tryMatch(',', $node));
        $this->mustMatch(')', $node, NULL, TRUE);
        return $node;
      case T_EMPTY:
      case T_EVAL:
        if ($this->currentType === T_EMPTY) {
          $node = new EmptyNode();
        }
        else {
          $node = new EvalNode();
        }
        $this->mustMatch($this->currentType, $node, 'functionReference');
        $this->mustMatch('(', $node);
        $node->appendChild($this->expr(), 'arguments');
        $this->mustMatch(')', $node, NULL, TRUE);
        return $node;
      case T_INCLUDE:
      case T_REQUIRE:
      case T_INCLUDE_ONCE:
      case T_REQUIRE_ONCE:
        if ($this->currentType === T_INCLUDE) {
          $node = new IncludeNode();
        }
        elseif ($this->currentType === T_INCLUDE_ONCE) {
          $node = new IncludeOnceNode();
        }
        elseif ($this->currentType === T_REQUIRE) {
          $node = new RequireNode();
        }
        else {
          $node = new RequireOnceNode();
        }
        $this->matchDocComment($node);
        $this->mustMatch($this->currentType, $node);
        $node->appendChild($this->expr(), 'expression');
        return $node;
      case T_NEW:
        return $this->newExpr();
      case T_LIST:
        return $this->_list();
      case T_EXIT:
        $node = new ExitNode();
        $this->mustMatch(T_EXIT, $node, NULL, TRUE);
        if ($this->currentType !== '(') {
          return $node;
        }
        $this->mustMatch('(', $node);
        if ($this->tryMatch(')', $node, NULL, TRUE)) {
          return $node;
        }
        if ($this->currentType === T_YIELD) {
          $node->appendChild($this->_yield(), 'status');
        }
        else {
          $node->appendChild($this->expr(), 'status');
        }
        $this->mustMatch(')', $node);
        return $node;
      case T_FUNCTION:
        return $this->anonymousFunction();
      case '`':
        return $this->backtick();
    }
    throw new ParserException($this->iterator->getSourcePosition(), "expression operand");
  }

  /**
   * Parse a backtick expression.
   * @return BacktickNode
   */
  private function backtick() {
    $node = new BacktickNode();
    $this->mustMatch('`', $node);
    $this->encapsList($node, '`', TRUE);
    $this->mustMatch('`', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse an anonymous function declaration.
   * @param Node $static
   * @return AnonymousFunctionNode
   */
  private function anonymousFunction(Node $static = NULL) {
    $node = new AnonymousFunctionNode();
    if ($static) {
      $node->appendChild($static);
    }
    $this->mustMatch(T_FUNCTION, $node);
    $this->tryMatch('&', $node, 'reference');
    $this->parameterList($node);
    if ($this->tryMatch(T_USE, $node)) {
      $this->mustMatch('(', $node);
      do {
        if ($this->currentType === '&') {
          $var = new ReferenceVariableNode();
          $this->mustMatch('&', $var);
          $this->mustMatch(T_VARIABLE, $var, 'variable');
          $node->appendChild($var, 'lexicalVariables');
        }
        else {
          $this->mustMatch(T_VARIABLE, $node, 'lexicalVariables');
        }
      } while ($this->tryMatch(',', $node));
      $this->mustMatch(')', $node);
    }
    $node->appendChild($this->innerStatementBlock(), 'body');
    return $node;
  }

  /**
   * Parse a new expression.
   * @return NewNode
   */
  private function newExpr() {
    $node = new NewNode();
    $this->mustMatch(T_NEW, $node);
    $node->appendChild($this->classNameReference(), 'className');
    if ($this->currentType === '(') {
      $this->functionCallParameterList($node);
    }
    return $node;
  }

  /**
   * Parse a class name reference.
   * @return Node
   */
  private function classNameReference() {
    switch ($this->currentType) {
      case T_STRING:
      case T_NS_SEPARATOR:
      case T_NAMESPACE:
        $namespace_path = $this->namespacePath();
        if ($this->currentType === T_DOUBLE_COLON) {
          $node = $this->staticMember($namespace_path);
          return $this->dynamicClassNameReference($node);
        }
        else {
          return $namespace_path;
        }
      case T_STATIC:
        $static_node = $this->mustMatchToken(T_STATIC);
        if ($this->currentType === T_DOUBLE_COLON) {
          $node = $this->staticMember($static_node);
          return $this->dynamicClassNameReference($node);
        }
        else {
          return $static_node;
        }
      default:
        if ($this->currentType === '$' && !$this->isLookAhead('{')) {
          return $this->dynamicClassNameReference($this->indirectReference());
        }
        $var_node = $this->referenceVariable();
        if ($this->currentType === T_DOUBLE_COLON) {
          $var_node = $this->staticMember($var_node);
        }
        return $this->dynamicClassNameReference($var_node);
    }
  }

  /**
   * Parse static member.
   * @param Node $var_node
   * @return ClassMemberLookupNode
   */
  private function staticMember($var_node) {
    if ($this->currentType === '$' && $this->isLookAhead('{')) {
      $node = new ClassMemberLookupNode();
      $node->appendChild($var_node, 'className');
      $this->mustMatch(T_DOUBLE_COLON, $node);
      $node->appendChild($this->indirectReference(), 'memberName');
      return $node;
    }
    else {
      $node = new ClassMemberLookupNode();
      $node->appendChild($var_node, 'className');
      $this->mustMatch(T_DOUBLE_COLON, $node);
      $node->appendChild($this->compoundVariable(), 'memberName');
      return $this->offsetVariable($node);
    }
  }

  /**
   * Parse a dynamic class name reference.
   * @param Node $object
   * @return Node
   */
  private function dynamicClassNameReference(Node $object) {
    $node = $object;
    while ($this->currentType === T_OBJECT_OPERATOR) {
      $node = new ObjectPropertyNode();
      $node->appendChild($object, 'object');
      $this->mustMatch(T_OBJECT_OPERATOR, $node);
      $node->appendChild($this->objectProperty(), 'property');
      $object = $node;
    }
    return $node;
  }

  /**
   * Parse array pair list.
   * @param ArrayNode $node the parent ArrayNode
   * @param int|string $terminator Token type that ends the pair list
   */
  private function arrayPairList(ArrayNode $node, $terminator) {
    do {
      if ($this->currentType === $terminator) {
        break;
      }
      $node->appendChild($this->arrayPair());
    } while ($this->tryMatch(',', $node));
  }

  /**
   * Parse static array pair list.
   * @param ArrayNode $node Array node to add elements to
   * @param int|string $terminator Token type that terminates the array pair list
   */
  private function staticArrayPairList(ArrayNode $node, $terminator) {
    do {
      if ($this->currentType === $terminator) {
        break;
      }
      $value = $this->staticScalar();
      if ($this->currentType === T_DOUBLE_ARROW) {
        $pair = new ArrayPairNode();
        $pair->appendChild($value, 'key');
        $this->mustMatch(T_DOUBLE_ARROW, $pair);
        $pair->appendChild($this->staticScalar(), 'value');
        $node->appendChild($pair, 'elements');
      }
      else {
        $node->appendChild($value, 'elements');
      }
    } while ($this->tryMatch(',', $node));
  }

  /**
   * Parse an array pair.
   * @return Node
   */
  private function arrayPair() {
    if ($this->currentType === '&') {
      return $this->writeVariable();
    }
    $node = $this->expr();
    if ($this->currentType === T_DOUBLE_ARROW) {
      $expr = $node;
      $node = new ArrayPairNode();
      $node->appendChild($expr, 'key');
      $this->mustMatch(T_DOUBLE_ARROW, $node);
      if ($this->currentType === '&') {
        $node->appendChild($this->writeVariable(), 'value');
      }
      else {
        $node->appendChild($this->expr(), 'value');
      }
    }
    return $node;
  }

  /**
   * Parse a write variable.
   * @return ReferenceVariableNode
   */
  private function writeVariable() {
    $node = new ReferenceVariableNode();
    $this->mustMatch('&', $node);
    $node->appendChild($this->variable(), 'variable');
    return $node;
  }

  /**
   * Parse an encaps list.
   * @param ComplexStringNode|HeredocNode|BacktickNode $node Interpolated string.
   * @param int|string $terminator Token type that terminates the encaps list
   * @param bool $encaps_whitespace_allowed
   */
  private function encapsList($node, $terminator, $encaps_whitespace_allowed = FALSE) {
    if (!$encaps_whitespace_allowed) {
      if ($this->tryMatch(T_ENCAPSED_AND_WHITESPACE, $node)) {
        $node->appendChild($this->encapsVar());
      }
    }
    while ($this->currentType !== NULL && $this->currentType !== $terminator) {
      $this->tryMatch(T_ENCAPSED_AND_WHITESPACE, $node) ||
        $node->appendChild($this->encapsVar());
    }
  }

  /**
   * Parse an encaps variable.
   * @return StringVariableNode
   * @throws ParserException
   */
  private function encapsVar() {
    static $offset_types = [T_STRING, T_NUM_STRING, T_VARIABLE];
    $node = new StringVariableNode();
    if ($this->tryMatch(T_DOLLAR_OPEN_CURLY_BRACES, $node)) {
      if ($this->tryMatch(T_STRING_VARNAME, $node)) {
        if ($this->tryMatch('[', $node)) {
          $node->appendChild($this->expr());
          $this->mustMatch(']', $node);
        }
      }
      else {
        $node->appendChild($this->expr());
      }
      $this->mustMatch('}', $node);
      return $node;
    }
    elseif ($this->tryMatch(T_CURLY_OPEN, $node)) {
      $node->appendChild($this->variable());
      $this->mustMatch('}', $node);
      return $node;
    }
    elseif ($this->mustMatch(T_VARIABLE, $node)) {
      if ($this->tryMatch('[', $node)) {
        if (!in_array($this->currentType, $offset_types)) {
          throw new ParserException($this->iterator->getSourcePosition(),
            'expected encaps_var_offset (T_STRING or T_NUM_STRING or T_VARIABLE)');
        }
        $node->appendChild($this->tryMatchToken($offset_types));
        $this->mustMatch(']', $node);
      }
      elseif ($this->tryMatch(T_OBJECT_OPERATOR, $node)) {
        $this->mustMatch(T_STRING, $node);
      }
      return $node;
    }
    throw new ParserException($this->iterator->getSourcePosition(), 'expected encaps variable');
  }

  /**
   * Parse expression operand given class name.
   * @param Node $class_name
   * @return Node
   */
  private function exprClass(Node $class_name) {
    $colon_node = new PartialNode();
    $this->mustMatch(T_DOUBLE_COLON, $colon_node);
    if ($this->currentType === T_STRING) {
      $class_constant = $this->mustMatchToken(T_STRING);
      if ($this->currentType === '(') {
        return $this->classMethodCall($class_name, $colon_node, $class_constant);
      }
      else {
        return $this->classConstant($class_name, $colon_node, $class_constant);
      }
    }
    elseif ($this->currentType === T_CLASS) {
      return $this->classNameScalar($class_name, $colon_node);
    }
    else {
      return $this->classVariable($class_name, $colon_node);
    }
  }

  /**
   * Construct a class constant.
   * @param $class_name
   * @param $colon_node
   * @param $class_constant
   * @return ClassConstantLookupNode
   */
  private function classConstant($class_name, $colon_node, $class_constant) {
    $node = new ClassConstantLookupNode();
    $node->appendChild($class_name, 'className');
    $node->mergeNode($colon_node);
    $node->appendChild($class_constant, 'constantName');
    return $node;
  }

  /**
   * Construct a class method call.
   * @param Node $class_name
   * @param ParentNode $colon_node
   * @param Node $method_name
   * @return Node
   */
  private function classMethodCall($class_name, $colon_node, $method_name) {
    $node = new ClassMethodCallNode();
    $node->appendChild($class_name, 'className');
    $node->mergeNode($colon_node);
    $node->appendChild($method_name, 'methodName');
    $this->functionCallParameterList($node);
    return $this->objectDereference($this->arrayDeference($node));
  }

  /**
   * Construct a class name scalar.
   * @param $class_name
   * @param $colon_node
   * @return ClassNameScalarNode
   */
  private function classNameScalar($class_name, $colon_node) {
    $node = new ClassNameScalarNode();
    $node->appendChild($class_name, 'className');
    $node->mergeNode($colon_node);
    $this->mustMatch(T_CLASS, $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a class variable given $class_name::
   * @param $class_name
   * @param $colon_node
   * @return Node
   */
  private function classVariable($class_name, $colon_node) {
    if ($this->currentType === '{') {
      // Must be $class_name::{expr()}().
      return $this->classMethodCall($class_name, $colon_node, $this->bracesExpr());
    }

    /*
     * Note $var_node contains possible array lookup, eg. $a[0]. This must
     * happen cause $class::$var[0]() is treated as $class::($var[0])();
     * However $class::$var[0] is treated as ($class::$var)[0], but in order
     * to determine between these two cases, the [0] has to be matched.
     */
    $var_node = $this->indirectReference();
    if ($this->currentType === '(') {
      return $this->classMethodCall($class_name, $colon_node, $var_node);
    }
    else {
      /*
       * Since $class::$var[0] is treated as ($class::$var)[0] then have
       * to replace the $var in $var[0] with $class::$var.
       */
      if ($var_node instanceof ArrayLookupNode) {
        // Find the member name, eg. $var
        $member_name = $var_node;
        while ($member_name instanceof ArrayLookupNode) {
          $member_name = $member_name->getArray();
        }
        // Replace the member name with ClassMemberLookupNode, eg. $class::$var
        $node = new ClassMemberLookupNode();
        $node->appendChild($class_name, 'className');
        $node->mergeNode($colon_node);
        $node->appendChild(clone $member_name, 'memberName');
        $member_name->replace($node);
        return $this->objectDereference($var_node);
      }
      else {
        $node = new ClassMemberLookupNode();
        $node->appendChild($class_name, 'className');
        $node->mergeNode($colon_node);
        $node->appendChild($var_node, 'memberName');
        return $this->objectDereference($node);
      }
    }
  }

  /**
   * Parse variable.
   * @return Node
   * @throws ParserException
   */
  private function variable() {
    switch ($this->currentType) {
      case T_STRING:
      case T_NS_SEPARATOR:
      case T_NAMESPACE:
        $namespace_path = $this->namespacePath();
        if ($this->currentType === '(') {
          return $this->functionCall($namespace_path);
        }
        elseif ($this->currentType === T_DOUBLE_COLON) {
          return $this->varClass($namespace_path);
        }
        break;
      case T_STATIC:
        $class_name = $this->mustMatchToken(T_STATIC);
        return $this->varClass($class_name);
      case '$':
      case T_VARIABLE:
        $var = $this->indirectReference();
        if ($this->currentType === '(') {
          return $this->functionCall($var, TRUE);
        }
        elseif (!($var instanceof VariableVariableNode) && $this->currentType === T_DOUBLE_COLON) {
          return $this->varClass($var);
        }
        else {
          return $this->objectDereference($var);
        }
    }
    throw new ParserException($this->iterator->getSourcePosition(), "expected variable");
  }

  /**
   * Parse variable given class name.
   * @param Node $class_name
   * @return Node
   */
  private function varClass(Node $class_name) {
    $colon_node = new PartialNode();
    $this->mustMatch(T_DOUBLE_COLON, $colon_node);
    if ($this->currentType === T_STRING) {
      $method_name = $this->mustMatchToken(T_STRING);
      return $this->classMethodCall($class_name, $colon_node, $method_name);
    }
    else {
      return $this->classVariable($class_name, $colon_node);
    }
  }

  /**
   * Apply any function call, array and object deference.
   * @param Node $function_reference
   * @param bool $dynamic TRUE if the function call is dynamic
   * @return Node
   */
  private function functionCall(Node $function_reference, $dynamic = FALSE) {
    if ($dynamic) {
      $node = new CallbackCallNode();
      $node->appendChild($function_reference, 'callback');
    }
    else {
      if ($function_reference instanceof NamespacePathNode && $function_reference->getChildCount() === 1 && $function_reference == 'define') {
        $node = new DefineNode();
      }
      else {
        $node = new FunctionCallNode();
      }
      $node->appendChild($function_reference, 'namespacePath');
    }
    $this->functionCallParameterList($node);
    return $this->objectDereference($this->arrayDeference($node));
  }

  /**
   * Apply any object dereference to object operand.
   * @param Node $object
   * @return Node
   */
  private function objectDereference(Node $object) {
    if ($this->currentType !== T_OBJECT_OPERATOR) {
      return $object;
    }
    $operator_node = new PartialNode();
    $this->mustMatch(T_OBJECT_OPERATOR, $operator_node);

    $object_property = $this->objectProperty();
    if ($this->currentType === '(') {
      $node = new ObjectMethodCallNode();
      $node->appendChild($object, 'object');
      $node->mergeNode($operator_node);
      $node->appendChild($object_property, 'methodName');
      $this->functionCallParameterList($node);
    }
    else {
      $node = new ObjectPropertyNode();
      $node->appendChild($object, 'object');
      $node->mergeNode($operator_node);
      $node->appendChild($object_property, 'property');
    }

    return $this->objectDereference($this->arrayDeference($node));
  }

  /**
   * Parse object property.
   * @return Node
   */
  private function objectProperty() {
    if ($this->currentType === T_STRING) {
      return $this->offsetVariable($this->mustMatchToken(T_STRING));
    }
    elseif ($this->currentType === '{') {
      return $this->offsetVariable($this->bracesExpr());
    }
    else {
      return $this->indirectReference();
    }
  }

  /**
   * Parse indirect variable reference.
   * @return Node
   */
  private function indirectReference() {
    if ($this->currentType === '$' && !$this->isLookAhead('{')) {
      $node = new VariableVariableNode();
      $this->mustMatch('$', $node);
      $node->appendChild($this->indirectReference(), 'variable');
      return $node;
    }
    return $this->referenceVariable();
  }

  /**
   * Parse variable reference.
   * @return Node
   */
  private function referenceVariable() {
    return $this->offsetVariable($this->compoundVariable());
  }

  /**
   * Apply any offset to variable.
   * @param Node $var
   * @return Node
   */
  private function offsetVariable(Node $var) {
    if ($this->currentType === '{') {
      $node = new ArrayLookupNode();
      $node->appendChild($var, 'array');
      $this->mustMatch('{', $node);
      $node->appendChild($this->expr(), 'key');
      $this->mustMatch('}', $node, NULL, TRUE);
      return $this->offsetVariable($node);
    }
    elseif ($this->currentType === '[') {
      $node = new ArrayLookupNode();
      $node->appendChild($var, 'array');
      $this->dimOffset($node);
      return $this->offsetVariable($node);
    }
    else {
      return $var;
    }
  }

  /**
   * Parse compound variable.
   * @return Node
   */
  private function compoundVariable() {
    if ($this->currentType === '$') {
      return $this->_compoundVariable();
    }
    else {
      return $this->mustMatchToken(T_VARIABLE);
    }
  }

  /**
   * Parse compound variable.
   * @return CompoundVariableNode
   */
  private function _compoundVariable() {
    $node = new CompoundVariableNode();
    $this->mustMatch('$', $node);
    $this->mustMatch('{', $node);
    $node->appendChild($this->expr(), 'expression');
    $this->mustMatch('}', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse braces expression.
   * @return DynamicNameNode
   */
  private function bracesExpr() {
    $node = new DynamicNameNode();
    $this->mustMatch('{', $node);
    $node->appendChild($this->expr());
    $this->mustMatch('}', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse dimensional offset.
   * @param ArrayLookupNode $node Node to append to
   */
  private function dimOffset(ArrayLookupNode $node) {
    $this->mustMatch('[', $node);
    if ($this->currentType !== ']') {
      $node->appendChild($this->expr(), 'key');
    }
    $this->mustMatch(']', $node, NULL, TRUE);
  }

  /**
   * Parse function call parameter list.
   * @param NewNode|FunctionCallNode|ClassMethodCallNode|ObjectMethodCallNode $node
   */
  private function functionCallParameterList($node) {
    $this->mustMatch('(', $node);
    if ($this->tryMatch(')', $node, NULL, TRUE)) {
      return;
    }
    if ($this->currentType === T_YIELD) {
      $node->appendChild($this->_yield(), 'arguments');
    } else {
      do {
        $node->appendChild($this->functionCallParameter(), 'arguments');
      } while ($this->tryMatch(',', $node));
    }
    $this->mustMatch(')', $node, NULL, TRUE);
  }

  /**
   * Parse function call parameter.
   * @return Node
   */
  private function functionCallParameter() {
    switch ($this->currentType) {
      case '&':
        return $this->writeVariable();
      case T_ELLIPSIS:
        $node = new EllipsisNode();
        $this->mustMatch(T_ELLIPSIS, $node);
        $node->appendChild($this->expr(), 'expression');
        return $node;
      default:
        return $this->expr();
    }
  }

  /**
   * Apply any array deference to operand.
   * @param Node $node
   * @return Node
   */
  private function arrayDeference(Node $node) {
    while ($this->currentType === '[') {
      $n = $node;
      $node = new ArrayLookupNode();
      $node->appendChild($n, 'array');
      $this->dimOffset($node);
    }
    return $node;
  }

  /**
   * Parse function declaration.
   * @return FunctionDeclarationNode
   */
  private function functionDeclaration() {
    $node = new FunctionDeclarationNode();
    $this->matchDocComment($node);
    $this->mustMatch(T_FUNCTION, $node);
    $this->tryMatch('&', $node, 'reference');
    $this->mustMatch(T_STRING, $node, 'name');
    $this->parameterList($node);
    $node->appendChild($this->innerStatementBlock(), 'body');
    return $node;
  }

  /**
   * Parse parameter list.
   * @param ClassMethodNode|InterfaceMethodNode|FunctionDeclarationNode|AnonymousFunctionNode|NewNode $node
   */
  private function parameterList($node) {
    $this->mustMatch('(', $node);
    if ($this->tryMatch(')', $node)) {
      return;
    }
    do {
      $node->appendChild($this->parameter(), 'parameters');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(')', $node, NULL, TRUE);
  }

  /**
   * Parse parameter.
   * @return ParameterNode
   */
  private function parameter() {
    $node = new ParameterNode();
    if ($type = $this->optionalClassType()) {
      $node->appendChild($type, 'classType');
    }
    $this->tryMatch('&', $node, 'reference');
    $this->tryMatch(T_ELLIPSIS, $node);
    $this->mustMatch(T_VARIABLE, $node, 'name', TRUE);
    if ($this->tryMatch('=', $node)) {
      $node->appendChild($this->staticScalar(), 'defaultValue');
    }
    return $node;
  }

  /**
   * Parse optional class type for parameter.
   * @return Node
   */
  private function optionalClassType() {
    static $array_callable_types = [T_ARRAY, T_CALLABLE];
    $node = NULL;
    if ($node = $this->tryMatchToken($array_callable_types)) {
      return $node;
    }
    elseif (in_array($this->currentType, self::$namespacePathTypes)) {
      return $this->namespacePath();
    }
    return NULL;
  }

  /**
   * Parse inner statement list.
   * @param StatementBlockNode $parent Node to append statements to
   * @param int|string $terminator Token type that terminates the statement list
   */
  private function innerStatementList(StatementBlockNode $parent, $terminator) {
    $this->matchHidden($parent);
    while ($this->currentType !== NULL && $this->currentType !== $terminator) {
      $parent->appendChild($this->innerStatement(), 'statements');
      $this->matchHidden($parent);
    }
  }

  /**
   * Parse inner statement block.
   * @return Node
   */
  private function innerStatementBlock() {
    $node = new StatementBlockNode();
    $this->mustMatch('{', $node);
    $this->innerStatementList($node, '}');
    $this->mustMatch('}', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse inner statement list for alternative control structures.
   * @param $terminator
   * @return Node
   */
  private function innerStatementListNode($terminator) {
    $node = new StatementBlockNode();
    $this->innerStatementList($node, $terminator);
    return $node;
  }

  /**
   * Parse an inner statement.
   * @return Node
   * @throws ParserException
   */
  private function innerStatement() {
    switch ($this->currentType) {
      case T_HALT_COMPILER:
        throw new ParserException($this->iterator->getSourcePosition(),
          "__halt_compiler can only be used from the outermost scope");
      case T_ABSTRACT:
      case T_FINAL:
      case T_CLASS:
        return $this->classDeclaration();
      case T_INTERFACE:
        return $this->interfaceDeclaration();
      case T_TRAIT:
        return $this->traitDeclaration();
      default:
        if ($this->currentType === T_FUNCTION) {
          if ($function_declaration = $this->functionDeclaration()) {
            return $function_declaration;
          }
        }
        return $this->statement();
    }
  }

  /**
   * Parse a namespace path.
   * @return NamespacePathNode
   */
  private function namespacePath() {
    $node = new NamespacePathNode();
    if ($this->tryMatch(T_NAMESPACE, $node)) {
      $this->mustMatch(T_NS_SEPARATOR, $node);
    }
    else {
      $this->tryMatch(T_NS_SEPARATOR, $node);
    }
    $this->mustMatch(T_STRING, $node, NULL, TRUE);
    while ($this->tryMatch(T_NS_SEPARATOR, $node)) {
      $this->mustMatch(T_STRING, $node, NULL, TRUE);
    }
    return $node;
  }

  /**
   * Parse a namespace declaration.
   * @return NamespaceNode
   */
  private function _namespace() {
    $node = new NamespaceNode();
    $this->matchDocComment($node);
    $this->mustMatch(T_NAMESPACE, $node);
    if ($this->currentType === T_STRING) {
      $node->appendChild($this->namespaceName(), 'name');
    }
    if ($this->tryMatch('{', $node)) {
      $node->appendChild($this->topStatementBlock('}'), 'body');
      $this->mustMatch('}', $node);
    }
    else {
      $this->mustMatch(';', $node, NULL, TRUE);
    }
    return $node;
  }

  /**
   * Parse a namespace name.
   * @return NamespacePathNode
   */
  private function namespaceName() {
    $node = new NamespacePathNode();
    $this->mustMatch(T_STRING, $node, NULL, TRUE);
    while ($this->tryMatch(T_NS_SEPARATOR, $node)) {
      $this->mustMatch(T_STRING, $node, NULL, TRUE);
    }
    return $node;
  }

  /**
   * Parse a use declaration list.
   * @return UseDeclarationStatementNode
   */
  private function _use() {
    $node = new UseDeclarationStatementNode();
    $this->mustMatch(T_USE, $node);
    $this->tryMatch(T_FUNCTION, $node) || $this->tryMatch(T_CONST, $node);
    do {
      $node->appendChild($this->useDeclaration(), 'declarations');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a use declaration.
   * @return UseDeclarationNode
   */
  private function useDeclaration() {
    $declaration = new UseDeclarationNode();
    $node = new NamespacePathNode();
    $this->tryMatch(T_NS_SEPARATOR, $node);
    $this->mustMatch(T_STRING, $node, NULL, TRUE);
    while ($this->tryMatch(T_NS_SEPARATOR, $node)) {
      $this->mustMatch(T_STRING, $node, NULL, TRUE);
    }
    $declaration->appendChild($node, 'namespacePath');
    if ($this->tryMatch(T_AS, $declaration)) {
      $this->mustMatch(T_STRING, $declaration, 'alias', TRUE);
    }
    return $declaration;
  }

  /**
   * Parse a class declaration.
   * @return ClassNode
   */
  private function classDeclaration() {
    $node = new ClassNode();
    $this->matchDocComment($node);
    $this->tryMatch(T_ABSTRACT, $node, 'abstract') || $this->tryMatch(T_FINAL, $node, 'final');
    $this->mustMatch(T_CLASS, $node);
    $this->mustMatch(T_STRING, $node, 'name');
    if ($this->tryMatch(T_EXTENDS, $node)) {
      $node->appendChild($this->namespacePath(), 'extends');
    }
    if ($this->tryMatch(T_IMPLEMENTS, $node)) {
      do {
        $node->appendChild($this->namespacePath(), 'implements');
      } while ($this->tryMatch(',', $node));
    }
    $this->mustMatch('{', $node);
    $is_abstract = $node->getAbstract() !== NULL;
    while ($this->currentType !== NULL && $this->currentType !== '}') {
      $node->appendChild($this->classStatement($is_abstract), 'statements');
      $this->matchHidden($node);
    }
    $this->mustMatch('}', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a class statement.
   * @param bool $is_abstract TRUE if the class is abstract
   * @return Node
   * @throws ParserException
   */
  private function classStatement($is_abstract) {
    if ($this->currentType === T_FUNCTION) {
      $modifiers = new ModifiersNode();
      $doc_comment = new PartialNode();
      $this->matchDocComment($doc_comment);
      return $this->classMethod($doc_comment, $modifiers);
    }
    elseif ($this->currentType === T_VAR) {
      $doc_comment = new PartialNode();
      $this->matchDocComment($doc_comment);
      $modifiers = new ModifiersNode();
      $this->mustMatch(T_VAR, $modifiers, 'visibility');
      return $this->classMemberList($doc_comment, $modifiers);
    }
    elseif ($this->currentType === T_CONST) {
      return $this->_const();
    }
    elseif ($this->currentType === T_USE) {
      return $this->traitUse();
    }
    // Match modifiers
    $doc_comment = new PartialNode();
    $this->matchDocComment($doc_comment);
    $modifiers = new ModifiersNode();
    while ($this->iterator->hasNext()) {
      switch ($this->currentType) {
        case T_PUBLIC:
        case T_PROTECTED:
        case T_PRIVATE:
          if ($modifiers->getVisibility()) {
            throw new ParserException(
              $this->iterator->getSourcePosition(),
              "can only have one visibility modifier on class member/method."
            );
          }
          $this->mustMatch($this->currentType, $modifiers, 'visibility');
          break;
        case T_STATIC:
          if ($modifiers->getStatic()) {
            throw new ParserException(
              $this->iterator->getSourcePosition(), "duplicate modifier");
          }
          $this->mustMatch(T_STATIC, $modifiers, 'static');
          break;
        case T_FINAL:
          if ($modifiers->getFinal()) {
            throw new ParserException(
              $this->iterator->getSourcePosition(), "duplicate modifier");
          }
          if ($modifiers->getAbstract()) {
            throw new ParserException(
              $this->iterator->getSourcePosition(),
              "can not use final modifier on abstract method");
          }
          $this->mustMatch(T_FINAL, $modifiers, 'final');
          break;
        case T_ABSTRACT:
          if ($modifiers->getAbstract()) {
            throw new ParserException(
              $this->iterator->getSourcePosition(), "duplicate modifier");
          }
          if ($modifiers->getFinal()) {
            throw new ParserException(
              $this->iterator->getSourcePosition(),
              "can not use abstract modifier on final method");
          }
          if (!$is_abstract) {
            throw new ParserException(
              $this->iterator->getSourcePosition(),
              "can not use abstract modifier in non-abstract class");
          }
          $this->mustMatch(T_ABSTRACT, $modifiers, 'abstract');
          break;
        case T_FUNCTION:
          return $this->classMethod($doc_comment, $modifiers);
        case T_VARIABLE:
          return $this->classMemberList($doc_comment, $modifiers);
        default:
          throw new ParserException(
            $this->iterator->getSourcePosition(),
            "invalid class statement");
      }
    }
    throw new ParserException(
      $this->iterator->getSourcePosition(),
      "invalid class statement");
  }

  /**
   * Parse a class member list.
   * @param PartialNode|null $doc_comment DocBlock associated with method
   * @param ModifiersNode $modifiers Member modifiers
   * @return ClassMemberListNode
   * @throws ParserException
   */
  private function classMemberList($doc_comment, ModifiersNode $modifiers) {
    // Modifier checks
    if ($modifiers->getAbstract()) {
      throw new ParserException(
        $this->iterator->getSourcePosition(),
        "members can not be declared abstract");
    }
    if ($modifiers->getFinal()) {
      throw new ParserException(
        $this->iterator->getSourcePosition(),
        "members can not be declared final");
    }
    $node = new ClassMemberListNode();
    $node->mergeNode($doc_comment);
    $node->appendChild($modifiers, 'modifiers');
    do {
      $node->appendChild($this->classMember(), 'members');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a class member.
   * @return ClassMemberNode
   */
  private function classMember() {
    $node = new ClassMemberNode();
    $this->mustMatch(T_VARIABLE, $node, 'name');
    if ($this->tryMatch('=', $node)) {
      $node->appendChild($this->staticScalar(), 'initialValue');
    }
    return $node;
  }

  /**
   * Parse a class method
   * @param PartialNode|null $doc_comment DocBlock associated with method
   * @param ModifiersNode $modifiers Method modifiers
   * @return ClassMethodNode
   */
  private function classMethod($doc_comment, ModifiersNode $modifiers) {
    $node = new ClassMethodNode();
    $node->mergeNode($doc_comment);
    $node->appendChild($modifiers, 'modifiers');
    $this->mustMatch(T_FUNCTION, $node);
    $this->tryMatch('&', $node, 'reference');
    $this->mustMatch(T_STRING, $node, 'name');
    $this->parameterList($node);
    if ($modifiers->getAbstract()) {
      $this->mustMatch(';', $node, NULL, TRUE);
      return $node;
    }
    $node->appendChild($this->innerStatementBlock(), 'body');
    return $node;
  }

  /**
   * Parse a trait use statement.
   * @return TraitUseNode
   */
  private function traitUse() {
    $node = new TraitUseNode();
    $this->mustMatch(T_USE, $node);
    // trait_list
    do {
      $node->appendChild($this->namespacePath(), 'traits');
    } while ($this->tryMatch(',', $node));
    // trait_adaptations
    if ($this->tryMatch('{', $node)) {
      while ($this->currentType !== NULL && $this->currentType !== '}') {
        $node->appendChild($this->traitAdaptation(), 'adaptations');
        $this->matchHidden($node);
      }
      $this->mustMatch('}', $node, NULL, TRUE);
      return $node;
    }
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a trait adaptation statement.
   * @return Node
   */
  private function traitAdaptation() {
    /** @var NamespacePathNode $qualified_name */
    $qualified_name = $this->namespacePath();
    if ($qualified_name->getChildCount() === 1 && $this->currentType !== T_DOUBLE_COLON) {
      return $this->traitAlias($qualified_name);
    }
    $node = new TraitMethodReferenceNode();
    $node->appendChild($qualified_name, 'traitName');
    $this->mustMatch(T_DOUBLE_COLON, $node);
    $this->mustMatch(T_STRING, $node, 'methodReference', TRUE);
    if ($this->currentType === T_AS) {
      return $this->traitAlias($node);
    }
    $method_reference_node = $node;
    $node = new TraitPrecedenceNode();
    $node->appendChild($method_reference_node, 'traitMethodReference');
    $this->mustMatch(T_INSTEADOF, $node);
    do {
      $node->appendChild($this->namespacePath(), 'traitNames');
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse a trait alias.
   * @param TraitMethodReferenceNode|NamespacePathNode $trait_method_reference
   * @return TraitAliasNode
   */
  private function traitAlias($trait_method_reference) {
    $node = new TraitAliasNode();
    $node->appendChild($trait_method_reference, 'traitMethodReference');
    $this->mustMatch(T_AS, $node);
    if ($trait_modifier = $this->tryMatchToken(self::$visibilityTypes)) {
      $node->appendChild($trait_modifier, 'visibility');
      $this->tryMatch(T_STRING, $node, 'alias');
      $this->mustMatch(';', $node, NULL, TRUE);
      return $node;
    }
    $this->mustMatch(T_STRING, $node, 'alias');
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse an interface declaration.
   * @return Node
   */
  private function interfaceDeclaration() {
    $node = new InterfaceNode();
    $this->matchDocComment($node);
    $this->mustMatch(T_INTERFACE, $node);
    $this->mustMatch(T_STRING, $node, 'name');
    if ($this->tryMatch(T_EXTENDS, $node)) {
      do {
        $node->appendChild($this->namespacePath(), 'extends');
      } while ($this->tryMatch(',', $node));
    }
    $this->mustMatch('{', $node);
    while ($this->currentType !== NULL && $this->currentType !== '}') {
      if ($this->currentType === T_CONST) {
        $node->appendChild($this->_const(), 'statements');
      }
      else {
        $node->appendChild($this->interfaceMethod(), 'statements');
      }
      $this->matchHidden($node);
    }
    $this->mustMatch('}', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Parse an interface method declaration.
   * @return InterfaceMethodNode
   * @throws ParserException
   */
  private function interfaceMethod() {
    static $visibility_keyword_types = [T_PUBLIC, T_PROTECTED, T_PRIVATE];
    $node = new InterfaceMethodNode();
    $this->matchDocComment($node);
    $is_static = $this->tryMatch(T_STATIC, $node);
    while (in_array($this->currentType, $visibility_keyword_types)) {
      if ($node->getVisibility()) {
        throw new ParserException(
          $this->iterator->getSourcePosition(),
          "can only have one visibility modifier on interface method."
        );
      }
      $this->mustMatch($this->currentType, $node, 'visibility');
    }
    !$is_static && $this->tryMatch(T_STATIC, $node);
    $this->mustMatch(T_FUNCTION, $node);
    $this->tryMatch('&', $node, 'reference');
    $this->mustMatch(T_STRING, $node, 'name');
    $this->parameterList($node);
    $this->mustMatch(';', $node, NULL, TRUE);
    return $node;
  }


  /**
   * Parse a trait declaration.
   * @return TraitNode
   */
  private function traitDeclaration() {
    $node = new TraitNode();
    $this->matchDocComment($node);
    $this->mustMatch(T_TRAIT, $node);
    $this->mustMatch(T_STRING, $node, 'name');
    if ($this->tryMatch(T_EXTENDS, $node)) {
      $node->appendChild($this->namespacePath(), 'extends');
    }
    if ($this->tryMatch(T_IMPLEMENTS, $node)) {
      do {
        $node->appendChild($this->namespacePath(), 'implements');
      } while ($this->tryMatch(',', $node));
    }
    $this->mustMatch('{', $node);
    while ($this->currentType !== NULL && $this->currentType !== '}') {
      $node->appendChild($this->classStatement(TRUE), 'statements');
      $this->matchHidden($node);
    }
    $this->mustMatch('}', $node, NULL, TRUE);
    return $node;
  }

  /**
   * Skip hidden tokens.
   */
  private function skipHidden() {
    $token = $this->iterator->current();
    // Skip whitespace and comment
    while ($token && ($token->getType() === T_COMMENT || $token->getType() === T_WHITESPACE)) {
      $this->skipped[] = $token;
      $token = $this->iterator->next();
    }
    while ($token && $token instanceof DocCommentNode) {
      $this->skippedDocComment = array();
      $this->docComment = $token;
      $token = $this->iterator->next();
      while ($token && ($token->getType() === T_COMMENT || $token->getType() === T_WHITESPACE)) {
        $this->skippedDocComment[] = $token;
        $token = $this->iterator->next();
      }
      if ($token && $token instanceof DocCommentNode) {
        // Merge skippedDocComment with skipped
        $this->skipped[] = $this->docComment;
        $this->skipped = array_merge($this->skipped, $this->skippedDocComment);
      }
    }
  }

  /**
   * Add any previously skipped tokens to $parent.
   * @param ParentNode $parent
   */
  private function addSkipped(ParentNode $parent) {
    $parent->appendChildren($this->skipped);
    $this->skipped = array();
    $this->matchDocComment($parent, NULL);
  }

  /**
   * Match hidden tokens and add to $parent.
   * @param ParentNode $parent
   */
  private function matchHidden(ParentNode $parent) {
    $this->skipHidden();
    $parent->appendChildren($this->skipped);
    $this->skipped = array();
  }

  private function matchDocComment(ParentNode $parent, $property_name = 'docComment') {
    if ($this->docComment) {
      $parent->appendChild($this->docComment, $property_name);
      $parent->appendChildren($this->skippedDocComment);
      $this->skippedDocComment = array();
      $this->docComment = NULL;
    }
  }

  /**
   * @param int $expected_type
   * @param ParentNode $parent
   * @param string $property_name
   * @param bool $maybe_last TRUE if this may be the last match for rule.
   * @return TokenNode
   * @throws ParserException
   */
  private function mustMatch($expected_type, ParentNode $parent, $property_name = NULL, $maybe_last = FALSE) {
    if ($this->currentType !== $expected_type) {
      throw new ParserException(
        $this->iterator->getSourcePosition(),
        'expected ' . TokenNode::typeName($expected_type));
    }
    $token_node = $this->current;
    $this->addSkipped($parent);
    $parent->appendChild($token_node, $property_name);
    $this->nextToken();
    if (!$maybe_last) {
      $this->addSkipped($parent);
    }
    return $token_node;
  }

  /**
   * @param int $expected_type
   * @param ParentNode $parent
   * @param string $property_name
   * @param bool $maybe_last TRUE if this may be the last match for rule.
   * @return TokenNode
   */
  private function tryMatch($expected_type, ParentNode $parent, $property_name = NULL, $maybe_last = FALSE) {
    if ($this->currentType !== $expected_type) {
      return NULL;
    }
    $token_node = $this->current;
    $this->addSkipped($parent);
    $parent->appendChild($token_node, $property_name);
    $this->nextToken();
    if (!$maybe_last) {
      $this->addSkipped($parent);
    }
    return $token_node;
  }

  /**
   * @param array $expected_types
   * @return TokenNode
   */
  private function tryMatchToken($expected_types) {
    if ($this->current === NULL) {
      return NULL;
    }
    foreach ($expected_types as $expected_type) {
      if ($expected_type === $this->currentType) {
        $token_node = $this->current;
        $this->nextToken();
        return $token_node;
      }
    }
    return NULL;
  }

  /**
   * @param int|string $expected_type Expected token type
   * @return TokenNode
   * @throws ParserException
   */
  private function mustMatchToken($expected_type) {
    if ($this->currentType !== $expected_type) {
      throw new ParserException(
        $this->iterator->getSourcePosition(),
        'expected ' . TokenNode::typeName($expected_type));
    }
    $token_node = $this->current;
    $this->nextToken();
    return $token_node;
  }

  /**
   * Move iterator to next non hidden token.
   */
  private function nextToken() {
    $this->iterator->next();
    $this->skipHidden();
    $this->current = $this->iterator->current();
    if ($this->current) {
      $this->currentType = $this->current->getType();
    }
    else {
      $this->currentType = NULL;
    }
  }

  /**
   * Look ahead from current position at tokens and check if the token at
   * offset is of an expected type, where the offset ignores hidden tokens.
   * @param int|string $expected_type Expected token type
   * @param int|string $skip_type (Optional) Additional token type to ignore
   * @return bool
   */
  private function isLookAhead($expected_type, $skip_type = NULL) {
    $token = NULL;
    for ($offset = 1; ; $offset++) {
      $token = $this->iterator->peek($offset);
      if ($token === NULL) {
        return FALSE;
      }
      if (!($token instanceof HiddenNode) && $token->getType() !== $skip_type) {
        return $expected_type === $token->getType();
      }
    }
    return FALSE;
  }
}
