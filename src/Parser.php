<?php
namespace Pharborist;

/**
 * Parses PHP tokens into syntax tree.
 * @package Pharborist
 */
class Parser {
  /**
   * Iterator over PHP tokens.
   * @var TokenIterator
   */
  private $iterator;

  /**
   * The previously parsed document comment.
   * @var Node
   */
  private $docComment = NULL;

  /**
   * Array of skipped hidden tokens.
   * @var Node[]
   */
  private $skipped = [];

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
   * @return Node Root node of the tree
   */
  public function buildTree(TokenIterator $iterator) {
    $this->iterator = $iterator;
    $top = new Node();
    $this->top = $top;
    // Parse any template statements that proceed the opening PHP tag.
    $this->templateStatementList($top);
    if ($this->tryMatch(T_OPEN_TAG, $top)) {
      $this->topStatementList($top);
    }
    return $top;
  }

  /**
   * Parse a file and return the parsed tree
   * @param string $filename Path to file
   * @return Node|bool
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
   * @return Node
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
   * @return Node
   *   The top-level node of the parsed tree
   */
  public static function parseSnippet($snippet) {
    $tree = self::parseSource('<?php ' . $snippet);
    // Strip the inserted opening php tag
    array_shift($tree->children);
    return $tree;
  }

  /**
   * Parse zero or more template statements.
   * @param Node $node Node to append matches to.
   * @throws ParserException
   */
  private function templateStatementList(Node $node) {
    while ($this->iterator->hasNext()) {
      if ($this->isTokenType(T_OPEN_TAG)) {
        return;
      }
      if ($child = $this->tryMatchToken(T_INLINE_HTML)) {
        $node->appendChild($child);
      }
      elseif ($child = $this->tryMatchToken(T_OPEN_TAG_WITH_ECHO)) {
        $node->appendChild($this->echoTagStatement());
      }
      else {
        throw new ParserException($this->iterator->getSourcePosition(),
          'expected PHP opening tag, but got ' . $this->iterator->getTokenText());
      }
    }
  }

  /**
   * Parse an echo PHP (eg. <?=$a?>) statement.
   * @return Node
   */
  private function echoTagStatement() {
    $node = new Node();
    $this->mustMatch(T_OPEN_TAG_WITH_ECHO, $node);
    $node->appendChild($this->exprList());
    $this->mustMatch(T_CLOSE_TAG, $node);
    return $node;
  }

  /**
   * Parse a list of top level statements.
   * @param Node $node Node to append matches to
   * @param string $terminator Character that ends the statement list
   */
  private function topStatementList(Node $node, $terminator = '') {
    $this->matchHidden($node);
    while ($this->iterator->hasNext() &&
      !$this->isTokenType($terminator) &&
      ($statement = $this->topStatement())) {
      $node->appendChild($statement);
      $this->matchHidden($node);
    }
    $this->matchHidden($node);
  }

  /**
   * Parse a block of top level statements.
   * @param string $terminator Character that ends the statement block
   * @return Node
   */
  private function topStatementBlock($terminator = '') {
    $node = new Node();
    $this->topStatementList($node, $terminator);
    return $node;
  }

  /**
   * Parse a top level statement.
   * @return Node
   */
  private function topStatement() {
    switch ($this->getTokenType()) {
      case T_NAMESPACE:
        return $this->_namespace();
      case T_USE:
        return $this->_use();
      case T_FUNCTION:
        return $this->functionDeclaration();
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
        $node = new Node();
        $this->mustMatch(T_HALT_COMPILER, $node);
        $this->mustMatch('(', $node);
        $this->mustMatch(')', $node);
        $this->mustMatch(';', $node);
        return $node;
      default:
        return $this->statement();
    }
  }

  /**
   * Parse a constant declaration list.
   * @return ConstantDeclarationListNode
   */
  private function _const() {
    $node = new ConstantDeclarationListNode();
    $this->mustMatch(T_CONST, $node);
    do {
      $node->declarations[] = $node->appendChild($this->constDeclaration());
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a constant declaration.
   * @return ConstantDeclarationNode
   */
  private function constDeclaration() {
    $node = new ConstantDeclarationNode();
    $node->name = $this->mustMatch(T_STRING, $node, TRUE);
    if ($this->mustMatch('=', $node)) {
      $node->value = $node->appendChild($this->staticScalar());
    }
    return $node;
  }

  /**
   * Parse a statement.
   * @return Node
   */
  private function statement() {
    switch ($this->getTokenType()) {
      case T_CLOSE_TAG:
        // A close tag escapes into template mode.
        $node = new Node();
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
        $node = new Node();
        $node->appendChild($this->_yield());
        $this->mustMatch(';', $node);
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
        // Check if static variable list.
        $this->mark();
        $node = new Node();
        $this->mustMatch(T_STATIC, $node);
        if ($this->isTokenType(T_VARIABLE)) {
          return $this->staticVariableList($node);
        }
        else {
          $this->rewind();
          return $this->exprStatement();
        }
      case T_STRING:
        // Check if goto label.
        $this->mark();
        $node = new Node();
        $this->mustMatch(T_STRING, $node);
        if ($this->tryMatch(':', $node, TRUE)) {
          return $node;
        }
        else {
          $this->rewind();
          return $this->exprStatement();
        }
      default:
        return $this->exprStatement();
    }
  }

  /**
   * Parse a static variable list.
   * @param Node $node static token.
   * @return Node
   */
  private function staticVariableList(Node $node) {
    do {
      $node->appendChild($this->staticVariable());
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a static variable.
   * @return Node
   */
  private function staticVariable() {
    $node = new Node();
    $this->mustMatch(T_VARIABLE, $node);
    if ($this->tryMatch('=', $node)) {
      $node->appendChild($this->staticScalar());
    }
    return $node;
  }

  /**
   * Parse expression statement.
   * @return Node
   */
  private function exprStatement() {
    $node = new Node();
    $this->matchHidden($node);
    $node->appendChild($this->expr());
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse parenthesis expression.
   * @return Node
   */
  private function parenExpr() {
    $node = new Node();
    $this->mustMatch('(', $node);
    $node->appendChild($this->expr());
    $this->mustMatch(')', $node, TRUE);
    return $node;
  }

  /**
   * Parse if control structure.
   * @return IfNode
   */
  private function _if() {
    $node = new IfNode();
    $this->mustMatch(T_IF, $node);
    $node->condition = $node->appendChild($this->parenExpr());
    if ($this->tryMatch(':', $node)) {
      $node->then = $node->appendChild($this->innerIfInnerStatementList());
      while ($this->isTokenType(T_ELSEIF)) {
        $elseIf = new ElseIfNode();
        $this->mustMatch(T_ELSEIF, $elseIf);
        $elseIf->condition = $elseIf->appendChild($this->parenExpr());
        $this->mustMatch(':', $elseIf);
        $elseIf->then = $elseIf->appendChild($this->innerIfInnerStatementList());
        $node->elseIfList[] = $node->appendChild($elseIf);
      }
      if ($this->tryMatch(T_ELSE, $node)) {
        $this->mustMatch(':', $node);
        $node->else = $node->appendChild($this->innerStatementListNode(T_ENDIF));
      }
      $this->mustMatch(T_ENDIF, $node);
      $this->mustMatch(';', $node, TRUE);
      return $node;
    }
    else {
      $this->matchHidden($node);
      $node->appendChild($this->statement());
      while ($this->isTokenType(T_ELSEIF)) {
        $elseIf = new ElseIfNode();
        $this->mustMatch(T_ELSEIF, $elseIf);
        $elseIf->condition = $elseIf->appendChild($this->parenExpr());
        $this->matchHidden($elseIf);
        $elseIf->then = $elseIf->appendChild($this->statement());
        $node->elseIfList[] = $node->appendChild($elseIf);
      }
      if ($this->tryMatch(T_ELSE, $node)) {
        $node->else = $node->appendChild($this->statement());
      }
      return $node;
    }
  }

  /**
   * Parse statements for alternative if syntax.
   * @return Node
   */
  private function innerIfInnerStatementList() {
    $node = new Node();
    $this->matchHidden($node);
    while ($this->iterator->hasNext() && !$this->isTokenType(T_ELSEIF, T_ELSE, T_ENDIF)) {
      $node->appendChild($this->innerStatement());
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
    $node->condition = $node->appendChild($this->parenExpr());
    if ($this->tryMatch(':', $node)) {
      $node->body = $node->appendChild($this->innerStatementListNode(T_ENDWHILE));
      $this->mustMatch(T_ENDWHILE, $node);
      $this->mustMatch(';', $node, TRUE);
      return $node;
    }
    else {
      $this->matchHidden($node);
      $node->body = $node->appendChild($this->statement());
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
    $node->body = $node->appendChild($this->statement());
    $this->mustMatch(T_WHILE, $node);
    $node->condition = $node->appendChild($this->parenExpr());
    $this->mustMatch(';', $node, TRUE);
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
    $node->initial = $this->forExpr($node, ';');
    $node->condition = $this->forExpr($node, ';');
    $node->step = $this->forExpr($node, ')', TRUE);
    if ($this->tryMatch(':', $node)) {
      $node->body = $node->appendChild($this->innerStatementListNode(T_ENDFOR));
      $this->mustMatch(T_ENDFOR, $node);
      $this->mustMatch(';', $node, TRUE);
      return $node;
    }
    else {
      $this->matchHidden($node);
      $node->body = $node->appendChild($this->statement());
      return $node;
    }
  }

  /**
   * Parse a for expression.
   * @param ForNode $parent Parent for node
   * @param int|string $terminator Token type that terminates the for expression
   * @param bool $is_last TRUE if last for expression
   * @return Node
   */
  private function forExpr(ForNode $parent, $terminator, $is_last = FALSE) {
    if ($this->isTokenType($terminator)) {
      $expr = new Node();
      $parent->appendChild($expr);
      $this->mustMatch($terminator, $parent);
      return $expr;
    }
    $node = new Node();
    $expr = $this->exprList();
    $node->appendChild($expr);
    $node = $parent->appendChild($node);
    $this->mustMatch($terminator, $parent, $is_last);
    return $node;
  }

  /**
   * Parse a switch control structure.
   * @return SwitchNode
   */
  private function _switch() {
    $node = new SwitchNode();
    $this->mustMatch(T_SWITCH, $node);
    $node->switchOn = $node->appendChild($this->parenExpr());
    if ($this->tryMatch(':', $node)) {
      $this->tryMatch(';', $node);
      $node->caseList = $node->appendChild($this->caseList(T_ENDSWITCH));
      $this->mustMatch(T_ENDSWITCH, $node);
      $this->mustMatch(';', $node, TRUE);
      return $node;
    }
    else {
      $this->mustMatch('{', $node);
      $this->tryMatch(';', $node);
      $node->caseList = $node->appendChild($this->caseList('}'));
      $this->mustMatch('}', $node, TRUE);
      return $node;
    }
  }

  /**
   * Parse list of case statements.
   * @param int|string $terminator Token type that terminates the list
   * @return Node
   */
  private function caseList($terminator) {
    $node = new Node();
    while ($this->iterator->hasNext() && !$this->isTokenType($terminator)) {
      $node->appendChild($this->caseStatement($terminator));
    }
    return $node;
  }

  /**
   * Parse a case statement.
   * @param int|string $terminator Token type that terminates statement list
   * @return CaseNode|DefaultNode
   * @throws ParserException
   */
  private function caseStatement($terminator) {
    if ($this->isTokenType(T_CASE)) {
      $node = new CaseNode();
      $this->mustMatch(T_CASE, $node);
      $node->matchOn = $node->appendChild($this->expr(TRUE));
      if (!$this->tryMatch(':', $node, TRUE) && !$this->tryMatch(';', $node, TRUE)) {
        throw new ParserException($this->iterator->getSourcePosition(), 'expected :');
      }
      $node->body = $node->appendChild($this->innerCaseStatementList($terminator));
      return $node;
    }
    elseif ($this->isTokenType(T_DEFAULT)) {
      $node = new DefaultNode();
      $this->mustMatch(T_DEFAULT, $node);
      if (!$this->tryMatch(':', $node, TRUE) && !$this->tryMatch(';', $node, TRUE)) {
        throw new ParserException($this->iterator->getSourcePosition(), 'expected :');
      }
      $node->body = $node->appendChild($this->innerCaseStatementList($terminator));
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
    $node = new Node();
    $this->matchHidden($node);
    while ($this->iterator->hasNext() && !$this->isTokenType($terminator, T_CASE, T_DEFAULT)) {
      $node->appendChild($this->innerStatement());
      $this->matchHidden($node);
    }
    return $node;
  }

  /**
   * Parse a break statement.
   * @return Node
   */
  private function _break() {
    $node = new Node();
    $this->mustMatch(T_BREAK, $node);
    if ($this->tryMatch(';', $node, TRUE)) {
      return $node;
    }
    $node->appendChild($this->expr());
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a continue statement.
   * @return Node
   */
  private function _continue() {
    $node = new Node();
    $this->mustMatch(T_CONTINUE, $node);
    if ($this->tryMatch(';', $node, TRUE)) {
      return $node;
    }
    $node->appendChild($this->expr());
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a return statement.
   * @return Node
   */
  private function _return() {
    $node = new Node();
    $this->mustMatch(T_RETURN, $node);
    if ($this->tryMatch(';', $node, TRUE)) {
      return $node;
    }
    $node->appendChild($this->expr());
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a yield expression.
   * @return Node
   */
  private function _yield() {
    $node = new Node();
    $this->mustMatch(T_YIELD, $node);
    $node->appendChild($this->expr());
    if ($this->tryMatch(T_DOUBLE_ARROW, $node)) {
      $node->appendChild($this->expr());
    }
    return $node;
  }

  /**
   * Parse a global variable declaration list.
   * @return Node
   */
  private function _global() {
    $node = new Node();
    $this->mustMatch(T_GLOBAL, $node);
    do {
      $node->appendChild($this->globalVar());
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse global variable.
   * @return Node
   * @throws ParserException
   */
  private function globalVar() {
    if ($var = $this->tryMatchToken(T_VARIABLE)) {
      return $var;
    }
    elseif ($this->isTokenType('$')) {
      $node = new Node();
      $this->mustMatch('$', $node);
      if ($this->tryMatch('{', $node)) {
        $node->appendChild($this->expr());
        $this->mustMatch('}', $node);
      }
      else {
        $node->appendChild($this->variable());
      }
      return $node;
    }
    throw new ParserException($this->iterator->getSourcePosition(), 'expected a global variable (eg. T_VARIABLE)');
  }

  /**
   * Parse echo statement.
   * @return Node
   */
  private function _echo() {
    $node = new Node();
    $this->mustMatch(T_ECHO, $node);
    do {
      $node->appendChild($this->expr());
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse an unset statement.
   * @return Node
   */
  private function _unset() {
    $node = new Node();
    $this->mustMatch(T_UNSET, $node);
    $this->mustMatch('(', $node);
    $node->appendChild($this->variableList());
    $this->mustMatch(')', $node);
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse list of variables.
   * @return Node
   */
  private function variableList() {
    $node = new Node();
    do {
      $node->appendChild($this->variable());
    } while ($this->tryMatch(',', $node));
    return $node;
  }

  /**
   * Parse foreach control structure.
   * @return ForeachNode
   */
  private function _foreach() {
    $node = new ForeachNode();
    $this->mustMatch(T_FOREACH, $node);
    $this->mustMatch('(', $node);
    $node->onEach = $node->appendChild($this->expr());
    $this->mustMatch(T_AS, $node);
    $node->value = $node->appendChild($this->foreachVariable());
    if ($this->tryMatch(T_DOUBLE_ARROW, $node)) {
      $node->key = $node->value;
      $node->value = $node->appendChild($this->foreachVariable());
    }
    $this->mustMatch(')', $node);
    if ($this->tryMatch(':', $node)) {
      $node->body = $node->appendChild($this->innerStatementListNode(T_ENDFOREACH));
      $this->mustMatch(T_ENDFOREACH, $node);
      $this->mustMatch(';', $node, TRUE);
      return $node;
    }
    else {
      $node->body = $node->appendChild($this->statement());
      return $node;
    }
  }

  /**
   * Parse a foreach variable.
   * @return Node
   */
  private function foreachVariable() {
    if ($this->isTokenType(T_LIST)) {
      return $this->_list();
    }
    else {
      if ($this->isTokenType('&')) {
        $node = new Node();
        $this->mustMatch('&', $node);
        $node->appendChild($this->variable());
        return $node;
      }
      else {
        return $this->variable();
      }
    }
  }

  /**
   * Parse a list() expression.
   * @return Node
   */
  private function _list() {
    $node = new Node();
    $this->mustMatch(T_LIST, $node);
    $this->mustMatch('(', $node);
    do {
      if ($this->tryMatch(')', $node, TRUE)) {
        return $node;
      }
      if (!$this->isTokenType(',')) {
        $node->appendChild($this->listElement());
      }
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(')', $node, TRUE);
    return $node;
  }

  /**
   * Parse an element from list() expression.
   * @return Node
   */
  private function listElement() {
    if ($this->isTokenType(T_LIST)) {
      return $this->_list();
    }
    else {
      return $this->variable();
    }
  }

  /**
   * Parse a declare statement.
   * @return Node
   */
  private function _declare() {
    $node = new Node();
    $this->mustMatch(T_DECLARE, $node);
    $node->appendChild($this->declareList());
    if ($this->tryMatch(':', $node)) {
      $node->appendChild($this->innerStatementListNode(T_ENDDECLARE));
      $this->mustMatch(T_ENDDECLARE, $node);
      $this->mustMatch(';', $node, TRUE);
      return $node;
    }
    else {
      $node->appendChild($this->statement());
      return $node;
    }
  }

  /**
   * Parse declares from declare statement.
   * @return Node
   */
  private function declareList() {
    $node = new Node();
    $this->mustMatch('(', $node);
    if ($this->tryMatch(')', $node, TRUE)) {
      return $node;
    }
    do {
      $child = new Node();
      $this->tryMatch(T_STRING, $child);
      if ($this->isTokenType('=')) {
        $c = $child;
        $child = new Node();
        $child->appendChild($c);
        $this->mustMatch('=', $child);
        $child->appendChild($this->staticScalar());
      }
      $node->appendChild($child);
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(')', $node, TRUE);
    return $node;
  }

  /**
   * Parse a try control structure.
   * @return Node
   */
  private function _try() {
    $node = new Node();
    $this->mustMatch(T_TRY, $node);
    $node->appendChild($this->innerStatementBlock());
    while ($this->tryMatch(T_CATCH, $node)) {
      $this->mustMatch('(', $node);
      $node->appendChild($this->namespacePath());
      $this->mustMatch(T_VARIABLE, $node);
      $this->mustMatch(')', $node);
      $node->appendChild($this->innerStatementBlock());
    }
    if ($this->tryMatch(T_FINALLY, $node)) {
      $this->mustMatch(T_FINALLY, $node);
      $node->appendChild($this->innerStatementBlock());
    }
    return $node;
  }

  /**
   * Parse a throw statement.
   * @return Node
   */
  private function _throw() {
    $node = new Node();
    $this->mustMatch(T_THROW, $node);
    $node->appendChild($this->expr());
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a goto statement.
   * @return Node
   */
  private function _goto() {
    $node = new Node();
    $this->mustMatch(T_GOTO, $node);
    $this->mustMatch(T_STRING, $node);
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a list of expressions.
   * @return Node
   */
  private function exprList() {
    $node = new Node();
    do {
      $node->appendChild($this->expr());
    } while ($this->tryMatch(',', $node));
    return $node;
  }

  /**
   * Parse an expression.
   * @param bool $is_case TRUE if parsing case expression.
   * @return Node
   * @throws ParserException
   */
  private function expr($is_case = FALSE) {
    // Group tokens into operands & operators to pass to the expression parser
    $expression_nodes = array();
    $ternary_count = 0;
    while ($this->iterator->hasNext() && !$this->isTokenType(';', ',', ')', ']', '}', T_AS, T_DOUBLE_ARROW)) {
      // Check balance of ? and : symbols for ternary operators
      if ($this->isTokenType('?')) {
        $ternary_count++;
      }
      elseif ($this->isTokenType(':')) {
        $ternary_count--;
        if ($ternary_count < 0) {
          if ($is_case) {
            break;
          }
          throw new ParserException(
            $this->iterator->getSourcePosition(), "unbalanced : with ? ternary operator");
        }
      }
      if ($this->isTokenType(':')) {
        $node = new Node();
        $node->type = ':';
        $this->mustMatch(':', $node);
        $expression_nodes[] = $node;
      }
      elseif ($op = $this->exprOperator()) {
        $expression_nodes[] = $op;
      }
      elseif ($operand = $this->exprOperand()) {
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
   * @return OperatorNode
   */
  private function exprOperator() {
    // Expression operator settings
    // Associativity, Precedence, Binary Operator, Unary Operator
    static $operators = array(
      T_LOGICAL_OR => array(OperatorNode::ASSOC_LEFT, 1, TRUE, FALSE),
      T_LOGICAL_XOR => array(OperatorNode::ASSOC_LEFT, 2, TRUE, FALSE),
      T_LOGICAL_AND => array(OperatorNode::ASSOC_LEFT, 3, TRUE, FALSE),
      '=' => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_AND_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_CONCAT_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_DIV_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_MINUS_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_MOD_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_MUL_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_OR_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_PLUS_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_SL_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_SR_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_XOR_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      T_POW_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE),
      '?' => array(OperatorNode::ASSOC_LEFT, 5, FALSE, FALSE),
      T_BOOLEAN_OR => array(OperatorNode::ASSOC_LEFT, 6, TRUE, FALSE),
      T_BOOLEAN_AND => array(OperatorNode::ASSOC_LEFT, 7, TRUE, FALSE),
      '|' => array(OperatorNode::ASSOC_LEFT, 8, TRUE, FALSE),
      '^' => array(OperatorNode::ASSOC_LEFT, 9, TRUE, FALSE),
      '&' => array(OperatorNode::ASSOC_LEFT, 10, TRUE, FALSE),
      T_IS_EQUAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE),
      T_IS_IDENTICAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE),
      T_IS_NOT_EQUAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE),
      T_IS_NOT_IDENTICAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE),
      '<' => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE),
      T_IS_SMALLER_OR_EQUAL => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE),
      T_IS_GREATER_OR_EQUAL => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE),
      '>' => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE),
      T_SL => array(OperatorNode::ASSOC_LEFT, 13, TRUE, FALSE),
      T_SR => array(OperatorNode::ASSOC_LEFT, 13, TRUE, FALSE),
      '+' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, TRUE),
      '-' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, TRUE),
      '.' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, FALSE),
      '*' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE),
      '/' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE),
      '%' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE),
      '!' => array(OperatorNode::ASSOC_RIGHT, 16, FALSE, TRUE),
      T_INSTANCEOF => array(OperatorNode::ASSOC_NONE, 17, TRUE, FALSE),
      T_INC => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_DEC => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_BOOL_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_INT_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_DOUBLE_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_STRING_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_ARRAY_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_OBJECT_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_UNSET_CAST  => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      '@' => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      '~' => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_POW => array(OperatorNode::ASSOC_RIGHT, 19, TRUE, FALSE),
      T_CLONE => array(OperatorNode::ASSOC_RIGHT, 20, FALSE, TRUE),
      T_PRINT => array(OperatorNode::ASSOC_RIGHT, 21, FALSE, TRUE),
    );
    $token_type = $this->getTokenType();
    if (array_key_exists($token_type, $operators)) {
      list($assoc, $precedence, $hasBinaryMode, $hasUnaryMode) = $operators[$token_type];
      $node = new OperatorNode();
      $node->type = $token_type;
      $node->associativity = $assoc;
      $node->precedence = $precedence;
      $node->hasBinaryMode = $hasBinaryMode;
      $node->hasUnaryMode = $hasUnaryMode;
      $this->mustMatch($token_type, $node);
      return $node;
    }
    return NULL;
  }

  /**
   * Parse an expression operand.
   * @return Node
   * @throws ParserException
   */
  private function exprOperand() {
    switch ($this->getTokenType()) {
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
        return $this->mustMatchToken($this->getTokenType());
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
        $node = new Node();
        $this->mustMatch('(', $node);
        if ($this->isTokenType(T_NEW)) {
          $node->appendChild($this->newExpr());
          $this->mustMatch(')', $node, TRUE);
          $node = $this->objectDereference($this->arrayDeference($node));
        }
        else {
          $node->appendChild($this->expr());
          $this->mustMatch(')', $node, TRUE);
        }
        return $node;
      case T_START_HEREDOC:
        $node = new Node();
        $this->mustMatch(T_START_HEREDOC, $node);
        if ($this->tryMatch(T_END_HEREDOC, $node, TRUE)) {
          return $node;
        }
        else {
          $node->appendChild($this->encapsList(T_END_HEREDOC, TRUE));
          $this->mustMatch(T_END_HEREDOC, $node, TRUE);
          return $node;
        }
      case '"':
        $node = new Node();
        $this->mustMatch('"', $node);
        $node->appendChild($this->encapsList('"'));
        $this->mustMatch('"', $node);
        return $node;
      case T_STRING:
      case T_NS_SEPARATOR:
      case T_NAMESPACE:
        $namespace_path = $this->namespacePath();
        if ($this->isTokenType(T_DOUBLE_COLON)) {
          return $this->exprClass($namespace_path);
        }
        else {
          return $this->exprFunctionCall($namespace_path);
        }
      case T_STATIC:
        $static = $this->mustMatchToken(T_STATIC);
        if ($this->isTokenType(T_FUNCTION)) {
          return $this->anonymousFunction($static);
        } else {
          return $this->exprClass($static);
        }
      case '$':
      case T_VARIABLE:
        $operand = $this->arrayDeference($this->indirectReference());
        if ($this->isTokenType(T_DOUBLE_COLON)) {
          return $this->exprClass($operand);
        }
        else {
          return $this->exprFunctionCall($operand);
        }
      case T_ISSET:
        $node = new Node();
        $this->mustMatch(T_ISSET, $node);
        $this->mustMatch('(', $node);
        $node->appendChild($this->exprList());
        $this->mustMatch(')', $node);
        return $node;
      case T_EMPTY:
      case T_EVAL:
        $node = new Node();
        $this->mustMatch($this->getTokenType(), $node);
        $this->mustMatch('(', $node);
        $node->appendChild($this->expr());
        $this->mustMatch(')', $node, TRUE);
        return $node;
      case T_INCLUDE:
      case T_REQUIRE:
      case T_INCLUDE_ONCE:
      case T_REQUIRE_ONCE:
        $node = new Node();
        $this->mustMatch($this->getTokenType(), $node);
        $node->appendChild($this->expr());
        return $node;
      case T_NEW:
        return $this->newExpr();
      case T_LIST:
        return $this->_list();
      case T_EXIT:
        $node = new Node();
        $this->mustMatch(T_EXIT, $node, TRUE);
        if (!$this->isTokenType('(')) {
          return $node->children[0];
        }
        $this->mustMatch('(', $node);
        if ($this->tryMatch(')', $node, TRUE)) {
          return $node;
        }
        $node->appendChild($this->expr());
        $this->mustMatch(')', $node);
        return $node;
      case T_FUNCTION:
        return $this->anonymousFunction();
    }
    throw new ParserException($this->iterator->getSourcePosition(), "expression operand");
  }

  /**
   * Parse an anonymous function declaration.
   * @param Node $static
   * @return Node
   */
  private function anonymousFunction(Node $static = NULL) {
    $node = new Node();
    if ($static) {
      $node->appendChild($static);
    }
    $this->mustMatch(T_FUNCTION, $node);
    $this->tryMatch('&', $node);
    $node->appendChild($this->parameterList());
    if ($this->tryMatch(T_USE, $node)) {
      $this->mustMatch('(', $node);
      $node->appendChild($this->lexicalVarList());
      $this->mustMatch(')', $node);
    }
    $node->appendChild($this->innerStatementBlock());
    return $node;
  }

  /**
   * Parse lexical variable list.
   * @return Node
   */
  private function lexicalVarList() {
    $node = new Node();
    do {
      if ($this->isTokenType('&')) {
        $var = new Node();
        $this->mustMatch('&', $var);
        $this->mustMatch(T_VARIABLE, $var);
        $node->appendChild($var);
      }
      else {
        $this->mustMatch(T_VARIABLE, $node);
      }
    } while ($this->tryMatch(',', $node));
    return $node;
  }

  /**
   * Parse a new expression.
   * @return Node
   */
  private function newExpr() {
    $node = new Node();
    $this->mustMatch(T_NEW, $node);
    $node->appendChild($this->classNameReference());
    $node->appendChild($this->ctorArguments());
    return $node;
  }

  /**
   * Parse a class name reference.
   * @return Node
   */
  private function classNameReference() {
    switch ($this->getTokenType()) {
      case T_STRING:
      case T_NS_SEPARATOR:
      case T_NAMESPACE:
        $namespace_path = $this->namespacePath();
        if ($this->isTokenType(T_DOUBLE_COLON)) {
          $node = new Node();
          $node->appendChild($namespace_path);
          $this->mustMatch(T_DOUBLE_COLON, $node);
          $node->appendChild($this->indirectReference());
          return $this->dynamicClassNameReference($node);
        }
        else {
          return $namespace_path;
        }
      case T_STATIC:
        $static = $this->mustMatchToken(T_STATIC);
        if ($this->isTokenType(T_DOUBLE_COLON)) {
          $node = new Node();
          $node->appendChild($static);
          $this->mustMatch(T_DOUBLE_COLON, $node);
          $node->appendChild($this->indirectReference());
          return $this->dynamicClassNameReference($node);
        }
        else {
          return $static;
        }
      default:
        return $this->dynamicClassNameReference($this->indirectReference());
    }
  }

  /**
   * Parse a dynamic class name reference.
   * @param Node $node
   * @return Node
   */
  private function dynamicClassNameReference(Node $node) {
    while ($this->isTokenType(T_OBJECT_OPERATOR)) {
      $n = $node;
      $node = new Node();
      $node->appendChild($n);
      $this->mustMatch(T_OBJECT_OPERATOR, $node);
      $node->appendChild($this->objectProperty());
    }
    return $node;
  }

  /**
   * Parse constructor arguments.
   * @return Node
   */
  private function ctorArguments() {
    if ($this->isTokenType('(')) {
      return $this->functionCallParameterList();
    } else {
      return new Node();
    }
  }

  /**
   * Parse array pair list.
   * @param ArrayNode $node the parent ArrayNode
   * @param int|string $terminator Token type that ends the pair list
   */
  private function arrayPairList(ArrayNode $node, $terminator) {
    while (!$this->isTokenType($terminator)) {
      $node->appendChild($this->arrayPair());
      $this->tryMatch(',', $node);
    }
  }

  /**
   * Parse an array pair.
   * @return Node
   */
  private function arrayPair() {
    if ($this->isTokenType('&')) {
      return $this->writeVariable();
    }
    $node = $this->expr();
    if ($this->isTokenType(T_DOUBLE_ARROW)) {
      $expr = $node;
      $node = new ArrayPairNode();
      $node->key = $node->appendChild($expr);
      $this->mustMatch(T_DOUBLE_ARROW, $node);
      if ($this->isTokenType('&')) {
        $node->value = $node->appendChild($this->writeVariable());
      }
      else {
        $node->value = $node->appendChild($this->expr());
      }
    }
    return $node;
  }

  /**
   * Parse a write variable.
   * @return Node
   */
  private function writeVariable() {
    $node = new Node();
    $this->mustMatch('&', $node);
    $node->appendChild($this->variable());
    return $node;
  }

  /**
   * Parse an encaps list.
   * @param int|string $terminator Token type that terminates the encaps list
   * @param bool $encaps_whitespace_allowed
   * @return Node
   */
  private function encapsList($terminator, $encaps_whitespace_allowed = FALSE) {
    $node = new Node();
    if (!$encaps_whitespace_allowed) {
      if ($this->tryMatch(T_ENCAPSED_AND_WHITESPACE, $node)) {
        $node->appendChild($this->encapsVar());
      }
    }
    while ($this->iterator->hasNext() && !$this->isTokenType($terminator)) {
      $this->tryMatch(T_ENCAPSED_AND_WHITESPACE, $node) ||
        $node->appendChild($this->encapsVar());
    }
    return $node;
  }

  /**
   * Parse an encaps variable.
   * @return Node
   * @throws ParserException
   */
  private function encapsVar() {
    $node = new Node();
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
        if (!$this->isTokenType(T_STRING, T_NUM_STRING, T_VARIABLE)) {
          throw new ParserException($this->iterator->getSourcePosition(),
            'expected encaps_var_offset (T_STRING or T_NUM_STRING or T_VARIABLE)');
        }
        $this->tryMatch(T_STRING, $node) ||
        $this->tryMatch(T_NUM_STRING, $node) ||
        $this->tryMatch(T_VARIABLE, $node);
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
    $node = new Node();
    $node->appendChild($class_name);
    $this->mustMatch(T_DOUBLE_COLON, $node);
    if ($this->tryMatch(T_STRING, $node)) {
      return $this->exprFunctionCall($node);
    }
    elseif ($this->tryMatch(T_CLASS, $node)) {
      return $node;
    }
    elseif ($this->isTokenType('{')) {
      $node->appendChild($this->bracesExpr());
      return $this->exprFunctionCall($node);
    }
    else {
      $node->appendChild($this->indirectReference());
      return $this->exprFunctionCall($node);
    }
  }

  /**
   * Parse expression operand where function call or object dereference.
   * @param Node
   * @return Node
   */
  private function exprFunctionCall(Node $node) {
    if ($this->isTokenType('(')) {
      return $this->functionCall($node);
    }
    else {
      return $this->objectDereference($node);
    }
  }

  /**
   * Parse variable.
   * @return Node
   * @throws ParserException
   */
  private function variable() {
    switch ($this->getTokenType()) {
      case T_STRING:
      case T_NS_SEPARATOR:
      case T_NAMESPACE:
        $namespace_path = $this->namespacePath();
        if ($this->isTokenType('(')) {
          return $this->functionCall($namespace_path);
        }
        elseif ($this->isTokenType(T_DOUBLE_COLON)) {
          return $this->varClass($namespace_path);
        }
      case T_STATIC:
        $class_name = $this->mustMatchToken(T_STATIC);
        return $this->varClass($class_name);
      case '$':
      case T_VARIABLE:
        $var = $this->indirectReference();
        if ($this->isTokenType('(')) {
          return $this->functionCall($var);
        }
        elseif ($this->isTokenType(T_DOUBLE_COLON)) {
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
    $node = new Node();
    $node->appendChild($class_name);
    $this->mustMatch(T_DOUBLE_COLON, $node);
    if ($this->tryMatch(T_STRING, $node)) {
      return $this->functionCall($node);
    }
    elseif ($this->isTokenType('{')) {
      $node->appendChild($this->bracesExpr());
      return $this->functionCall($node);
    }
    else {
      $node->appendChild($this->indirectReference());
      if ($this->isTokenType('(')) {
        return $this->functionCall($node);
      } else {
        return $this->objectDereference($node);
      }
    }
  }

  /**
   * Apply any function call, array and object deference.
   * @param Node $function_reference
   * @return Node
   */
  private function functionCall(Node $function_reference) {
    return $this->objectDereference($this->arrayDeference($this->_functionCall($function_reference)));
  }

  /**
   * Apply any function call to operand.
   * @param Node $function_reference
   * @return FunctionCallNode
   */
  private function _functionCall(Node $function_reference) {
    $node = new FunctionCallNode();
    $node->functionReference = $node->appendChild($function_reference);
    $node->arguments = $node->appendChild($this->functionCallParameterList());
    return $node;
  }

  /**
   * Apply any object dereference to object operand.
   * @param Node $object
   * @return Node
   */
  private function objectDereference(Node $object) {
    if (!$this->isTokenType(T_OBJECT_OPERATOR)) {
      return $object;
    }
    $node = new Node();
    $node->appendChild($object);
    $this->mustMatch(T_OBJECT_OPERATOR, $node);

    $object_property = $this->objectProperty();
    // is method
    if ($this->isTokenType('(')) {
      $object_property = $this->_functionCall($object_property);
    }

    $node->appendChild($object_property);
    return $this->objectDereference($this->arrayDeference($node));
  }

  /**
   * Parse object property.
   * @return Node
   */
  private function objectProperty() {
    if ($this->isTokenType(T_STRING)) {
      return $this->offsetVariable($this->mustMatchToken(T_STRING));
    }
    elseif ($this->isTokenType('{')) {
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
    if ($this->isTokenType('$')) {
      // Handle ${
      $this->mark();
      $dollar_sign = $this->mustMatchToken('$');
      if ($this->isTokenType('{')) {
        $this->rewind();
        return $this->offsetVariable($this->compoundVariable());
      }
      // Otherwise its an indirect reference
      $node = new Node();
      $node->appendChild($dollar_sign);
      $node->appendChild($this->indirectReference());
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
    if ($this->isTokenType('{')) {
      $node = new Node();
      $node->appendChild($var);
      $node->appendChild($this->bracesExpr());
      return $this->offsetVariable($node);
    }
    elseif ($this->isTokenType('[')) {
      $node = new Node();
      $node->appendChild($var);
      $node->appendChild($this->dimOffset());
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
    if ($this->isTokenType('$')) {
      $node = new Node();
      $this->mustMatch('$', $node);
      $this->mustMatch('{', $node);
      $node->appendChild($this->expr());
      $this->mustMatch('}', $node, TRUE);
      return $node;
    }
    else {
      return $this->mustMatchToken(T_VARIABLE);
    }
  }

  /**
   * Parse braces expression.
   * @return Node
   */
  private function bracesExpr() {
    $node = new Node();
    $this->mustMatch('{', $node);
    $node->appendChild($this->expr());
    $this->mustMatch('}', $node, TRUE);
    return $node;
  }

  /**
   * Parse dimensional offset.
   * @return Node
   */
  private function dimOffset() {
    $node = new Node();
    $this->mustMatch('[', $node);
    if (!$this->isTokenType(']')) {
      $node->appendChild($this->expr());
    }
    $this->mustMatch(']', $node, TRUE);
    return $node;
  }

  /**
   * Parse function call parameter list.
   * @return ArgumentListNode
   */
  private function functionCallParameterList() {
    $node = new ArgumentListNode();
    $this->mustMatch('(', $node);
    if ($this->tryMatch(')', $node, TRUE)) {
      return $node;
    }
    if ($this->isTokenType(T_YIELD)) {
      $node->arguments[] = $node->appendChild($this->_yield());
    } else {
      do {
        $node->arguments[] = $node->appendChild($this->functionCallParameter());
      } while ($this->tryMatch(',', $node));
    }
    $this->mustMatch(')', $node, TRUE);
    return $node;
  }

  /**
   * Parse function call parameter.
   * @return Node
   */
  private function functionCallParameter() {
    switch ($this->getTokenType()) {
      case '&':
        return $this->writeVariable();
      case T_ELLIPSIS:
        $node = new Node();
        $this->mustMatch(T_ELLIPSIS, $node);
        $node->appendChild($this->expr());
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
    while ($this->isTokenType('[')) {
      $n = $node;
      $node = new Node();
      $node->appendChild($n);
      $node->appendChild($this->dimOffset());
    }
    return $node;
  }

  /**
   * Parse function declaration.
   * @return Node
   */
  private function functionDeclaration() {
    $node = new Node();
    $this->mustMatch(T_FUNCTION, $node);
    $this->tryMatch('&', $node);
    if ($this->isTokenType('(')) {
      /*
       * As far as I can tell you can not apply any expression operators to
       * anonymous functions. So return the anonymous function if its at start
       * of statement instead of rewinding and applying the expr rule.
       */
      $node->appendChild($this->parameterList());
      if ($this->tryMatch(T_USE, $node)) {
        $this->mustMatch('(', $node);
        $node->appendChild($this->lexicalVarList());
        $this->mustMatch(')', $node);
      }
      $node->appendChild($this->innerStatementBlock());
      $this->mustMatch(';', $node);
      return $node;
    }
    else {
      $n = $node;
      $node = new FunctionDeclarationNode();
      $node->appendChildren($n->children);
      $node->name = $this->mustMatch(T_STRING, $node);
      $node->parameters = $node->appendChild($this->parameterList());
      $node->body = $node->appendChild($this->innerStatementBlock());
      return $node;
    }
  }

  /**
   * Parse parameter list.
   * @return ParameterListNode
   */
  private function parameterList() {
    $node = new ParameterListNode();
    $this->mustMatch('(', $node);
    if ($this->tryMatch(')', $node)) {
      return $node;
    }
    do {
      $node->parameters[] = $node->appendChild($this->parameter());
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(')', $node, TRUE);
    return $node;
  }

  /**
   * Parse parameter.
   * @return ParameterNode
   */
  private function parameter() {
    $node = new ParameterNode();
    if ($type = $this->optionalClassType()) {
      $node->classType = $node->appendChild($type);
    }
    $node->reference = $this->tryMatch('&', $node);
    $this->tryMatch(T_ELLIPSIS, $node);
    $node->name = $this->mustMatch(T_VARIABLE, $node, TRUE);
    if ($this->tryMatch('=', $node)) {
      $node->defaultValue = $node->appendChild($this->staticScalar());
    }
    return $node;
  }

  /**
   * Parse optional class type for parameter.
   * @return Node
   */
  private function optionalClassType() {
    $node = NULL;
    if ($node = $this->tryMatchToken(T_ARRAY, T_CALLABLE)) {
      return $node;
    }
    elseif ($this->isTokenType(T_STRING, T_NAMESPACE, T_NS_SEPARATOR)) {
      return $this->namespacePath();
    }
    return NULL;
  }

  /**
   * Parse a static scalar expression.
   * @return Node
   * @throws ParserException
   */
  private function staticScalar() {
    // Handle static array
    if ($this->isTokenType(T_ARRAY)) {
      $node = new Node();
      $this->mustMatch(T_ARRAY, $node);
      $this->mustMatch('(', $node);
      $node->appendChild($this->staticArrayPairList(')'));
      $this->mustMatch(')', $node, TRUE);
      return $node;
    }
    elseif ($this->isTokenType('[')) {
      $node = new Node();
      $this->mustMatch('[', $node);
      $node->appendChild($this->staticArrayPairList(']'));
      $this->mustMatch(']', $node, TRUE);
      return $node;
    }

    // Group tokens into operands & operators to pass to the expression parser
    $expression_nodes = array();
    $ternary_count = 0;
    while ($this->iterator->hasNext() && !$this->isTokenType(';', ',', ')', T_DOUBLE_ARROW)) {
      // Check balance of ? and : symbols for ternary operators
      if ($this->isTokenType('?')) {
        $ternary_count++;
      }
      elseif ($this->isTokenType(':')) {
        $ternary_count--;
        if ($ternary_count < 0) {
          throw new ParserException($this->iterator->getSourcePosition(),
            'unbalanced : with ? ternary operator');
        }
      }
      if ($this->isTokenType(':')) {
        $node = new Node();
        $node->type = ':';
        $this->mustMatch(':', $node);
        $expression_nodes[] = $node;
      }
      elseif ($op = $this->staticOperator()) {
        $expression_nodes[] = $op;
      }
      elseif ($operand = $this->staticOperand()) {
        $expression_nodes[] = $operand;
      }
      else {
        throw new ParserException($this->iterator->getSourcePosition(), 'invalid scalar expression');
      }
    }
    return $this->expressionParser->parse($expression_nodes);
  }

  /**
   * Parse static array pair list.
   * @param int|string $terminator Token type that terminates the array pair list
   * @return Node
   */
  private function staticArrayPairList($terminator) {
    $node = new Node();
    while (!$this->isTokenType($terminator)) {
      $node->appendChild($this->staticScalar());
      if ($this->tryMatch(T_DOUBLE_ARROW, $node)) {
        $node->appendChild($this->staticScalar());
      }
      $this->tryMatch(',', $node);
    }
    return $node;
  }

  /**
   * Parse static operator.
   * @return OperatorNode
   */
  private function staticOperator() {
    static $operators = array(
      T_LOGICAL_OR => array(OperatorNode::ASSOC_LEFT, 1, TRUE, FALSE),
      T_LOGICAL_XOR => array(OperatorNode::ASSOC_LEFT, 2, TRUE, FALSE),
      T_LOGICAL_AND => array(OperatorNode::ASSOC_LEFT, 3, TRUE, FALSE),
      '?' => array(OperatorNode::ASSOC_LEFT, 5, FALSE, FALSE),
      T_BOOLEAN_OR => array(OperatorNode::ASSOC_LEFT, 6, TRUE, FALSE),
      T_BOOLEAN_AND => array(OperatorNode::ASSOC_LEFT, 7, TRUE, FALSE),
      '|' => array(OperatorNode::ASSOC_LEFT, 8, TRUE, FALSE),
      '^' => array(OperatorNode::ASSOC_LEFT, 9, TRUE, FALSE),
      '&' => array(OperatorNode::ASSOC_LEFT, 10, TRUE, FALSE),
      T_IS_EQUAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE),
      T_IS_IDENTICAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE),
      T_IS_NOT_EQUAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE),
      T_IS_NOT_IDENTICAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE),
      '<' => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE),
      T_IS_SMALLER_OR_EQUAL => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE),
      T_IS_GREATER_OR_EQUAL => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE),
      '>' => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE),
      T_SL => array(OperatorNode::ASSOC_LEFT, 13, TRUE, FALSE),
      T_SR => array(OperatorNode::ASSOC_LEFT, 13, TRUE, FALSE),
      '+' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, TRUE),
      '-' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, TRUE),
      '.' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, FALSE),
      '*' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE),
      '/' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE),
      '%' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE),
      '!' => array(OperatorNode::ASSOC_RIGHT, 16, FALSE, TRUE),
      '~' => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE),
      T_POW => array(OperatorNode::ASSOC_RIGHT, 19, TRUE, FALSE),
    );
    $token_type = $this->getTokenType();
    if (array_key_exists($token_type, $operators)) {
      list($assoc, $precedence, $hasBinaryMode, $hasUnaryMode) = $operators[$token_type];
      $node = new OperatorNode();
      $node->type = $token_type;
      $node->associativity = $assoc;
      $node->precedence = $precedence;
      $node->hasBinaryMode = $hasBinaryMode;
      $node->hasUnaryMode = $hasUnaryMode;
      $this->mustMatch($token_type, $node);
      return $node;
    }
    return NULL;
  }

  /**
   * Parse static operand.
   * @return Node
   */
  private function staticOperand() {
    if ($scalar = $this->tryMatchToken(
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
      T_NS_C
    )) {
      return $scalar;
    }
    elseif ($this->isTokenType('(')) {
      $node = new Node();
      $this->mustMatch('(', $node);
      $node->appendChild($this->staticScalar());
      $this->mustMatch(')', $node, TRUE);
      return $node;
    }
    elseif ($this->isTokenType(T_STRING, T_NAMESPACE, T_NS_SEPARATOR)) {
      $namespace_path = $this->namespacePath();
      if ($this->isTokenType(T_DOUBLE_COLON)) {
        $node = new Node();
        $node->appendChild($namespace_path);
        $this->mustMatch(T_DOUBLE_COLON, $node);
        if ($this->tryMatch(T_CLASS, $node)) {
          return $node;
        }
        else {
          $this->mustMatch(T_STRING, $node);
          return $node;
        }
      }
      else {
        return $namespace_path;
      }
    }
    elseif ($this->isTokenType(T_STATIC)) {
      $node = new Node();
      $this->mustMatch(T_STATIC, $node);
      $this->mustMatch(T_DOUBLE_COLON, $node);
      if ($this->tryMatch(T_CLASS, $node)) {
        return $node;
      }
      else {
        $this->mustMatch(T_STRING, $node);
        return $node;
      }
    }
    elseif ($this->isTokenType(T_START_HEREDOC)) {
      $node = new Node();
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
   * Parse inner statement list.
   * @param Node $parent Node to append statements to
   * @param int|string $terminator Token type that terminates the statement list
   */
  private function innerStatementList(Node $parent, $terminator) {
    $this->matchHidden($parent);
    while ($this->iterator->hasNext() && !$this->isTokenType($terminator)) {
      $parent->appendChild($this->innerStatement());
      $this->matchHidden($parent);
    }
  }

  /**
   * Parse inner statement block.
   * @return Node
   */
  private function innerStatementBlock() {
    $node = new Node();
    $this->mustMatch('{', $node);
    $this->innerStatementList($node, '}');
    $this->mustMatch('}', $node, TRUE);
    return $node;
  }

  /**
   * Parse inner statement list for alternative control structures.
   * @param $terminator
   * @return Node
   */
  private function innerStatementListNode($terminator) {
    $node = new Node();
    $this->innerStatementList($node, $terminator);
    return $node;
  }

  /**
   * Parse an inner statement.
   * @return Node
   * @throws ParserException
   */
  private function innerStatement() {
    switch ($this->getTokenType()) {
      case T_HALT_COMPILER:
        throw new ParserException($this->iterator->getSourcePosition(),
          "__halt_compiler can only be used from the outermost scope");
      case T_FUNCTION:
        return $this->functionDeclaration();
      case T_ABSTRACT:
      case T_FINAL:
      case T_CLASS:
        return $this->classDeclaration();
      case T_INTERFACE:
        return $this->interfaceDeclaration();
      case T_TRAIT:
        return $this->traitDeclaration();
      default:
        return $this->statement();
    }
  }

  /**
   * Parse a namespace path.
   * @return Node
   */
  private function namespacePath() {
    $node = new Node();
    if ($this->tryMatch(T_NAMESPACE, $node)) {
      $this->mustMatch(T_NS_SEPARATOR, $node);
    }
    else {
      $this->tryMatch(T_NS_SEPARATOR, $node);
    }
    $this->mustMatch(T_STRING, $node, TRUE);
    while ($this->tryMatch(T_NS_SEPARATOR, $node)) {
      $this->mustMatch(T_STRING, $node, TRUE);
    }
    return $node;
  }

  /**
   * Parse a namespace declaration.
   * @return NamespaceNode
   */
  private function _namespace() {
    $node = new NamespaceNode();
    $this->mustMatch(T_NAMESPACE, $node);
    if ($this->isTokenType(T_STRING)) {
      $node->name = $node->appendChild($this->namespaceName());
    }
    if ($this->tryMatch('{', $node)) {
      $node->body = $node->appendChild($this->topStatementBlock('}'));
      $this->mustMatch('}', $node);
    }
    else {
      $this->mustMatch(';', $node, TRUE);
    }
    return $node;
  }

  /**
   * Parse a namespace name.
   * @return Node
   */
  private function namespaceName() {
    $node = new Node();
    $this->mustMatch(T_STRING, $node, TRUE);
    while ($this->tryMatch(T_NS_SEPARATOR, $node)) {
      $this->mustMatch(T_STRING, $node, TRUE);
    }
    return $node;
  }

  /**
   * Parse a use declaration list.
   * @return UseDeclarationListNode
   */
  private function _use() {
    $node = new UseDeclarationListNode();
    $this->mustMatch(T_USE, $node);
    $this->tryMatch(T_FUNCTION, $node) || $this->tryMatch(T_CONST, $node);
    do {
      $node->declarations[] = $node->appendChild($this->useDeclaration());
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a use declaration.
   * @return UseDeclarationNode
   */
  private function useDeclaration() {
    $declaration = new UseDeclarationNode();
    $node = new Node();
    $this->tryMatch(T_NS_SEPARATOR, $node);
    $this->mustMatch(T_STRING, $node, TRUE);
    while ($this->tryMatch(T_NS_SEPARATOR, $node)) {
      $this->mustMatch(T_STRING, $node, TRUE);
    }
    $declaration->namespacePath = $declaration->appendChild($node);
    if ($this->tryMatch(T_AS, $declaration)) {
      $declaration->alias = $this->mustMatch(T_STRING, $declaration, TRUE);
    }
    return $declaration;
  }

  /**
   * Parse a class declaration.
   * @return ClassNode
   */
  private function classDeclaration() {
    $node = new ClassNode();
    if ($abstract = $this->tryMatch(T_ABSTRACT, $node)) {
      $node->abstract = $abstract;
    }
    elseif ($final = $this->tryMatch(T_FINAL, $node)) {
      $node->final = $final;
    }
    $this->mustMatch(T_CLASS, $node);
    $node->name = $this->mustMatch(T_STRING, $node);
    if ($this->tryMatch(T_EXTENDS, $node)) {
      $node->extends = $node->appendChild($this->namespacePath());
    }
    if ($this->tryMatch(T_IMPLEMENTS, $node)) {
      do {
        $node->implements[] = $node->appendChild($this->namespacePath());
      } while ($this->tryMatch(',', $node));
    }
    $this->mustMatch('{', $node);
    while (!$this->isTokenType('}')) {
      $is_abstract = isset($node->abstract);
      $node->statements[] = $node->appendChild($this->classStatement($is_abstract));
    }
    $this->mustMatch('}', $node, TRUE);
    return $node;
  }

  /**
   * Parse a class statement.
   * @param bool $is_abstract TRUE if the class is abstract
   * @return Node
   * @throws ParserException
   */
  private function classStatement($is_abstract) {
    if ($this->isTokenType(T_FUNCTION)) {
      $modifiers = new ModifiersNode();
      return $this->classMethod($modifiers);
    }
    elseif ($this->isTokenType(T_VAR)) {
      $modifiers = new ModifiersNode();
      $modifiers->visibility = $this->mustMatch(T_VAR, $modifiers);
      return $this->classMemberList($modifiers);
    }
    elseif ($this->isTokenType(T_CONST)) {
      return $this->_const();
    }
    elseif ($this->isTokenType(T_USE)) {
      return $this->traitUse();
    }
    // Match modifiers
    $modifiers = new ModifiersNode();
    while ($this->iterator->hasNext()) {
      switch ($this->getTokenType()) {
        case T_PUBLIC:
        case T_PROTECTED:
        case T_PRIVATE:
          if ($modifiers->visibility) {
            throw new ParserException(
              $this->iterator->getSourcePosition(),
              "can only have one visibility modifier on class member/method."
            );
          }
          $modifiers->visibility = $this->mustMatch($this->getTokenType(), $modifiers);
          break;
        case T_STATIC:
          if ($modifiers->static) {
            throw new ParserException(
              $this->iterator->getSourcePosition(), "duplicate modifier");
          }
          $modifiers->static = $this->mustMatch(T_STATIC, $modifiers);
          break;
        case T_FINAL:
          if ($modifiers->final) {
            throw new ParserException(
              $this->iterator->getSourcePosition(), "duplicate modifier");
          }
          if ($modifiers->abstract) {
            throw new ParserException(
              $this->iterator->getSourcePosition(),
              "can not use final modifier on abstract method");
          }
          $modifiers->final = $this->mustMatch(T_FINAL, $modifiers);
          break;
        case T_ABSTRACT:
          if ($modifiers->abstract) {
            throw new ParserException(
              $this->iterator->getSourcePosition(), "duplicate modifier");
          }
          if ($modifiers->final) {
            throw new ParserException(
              $this->iterator->getSourcePosition(),
              "can not use abstract modifier on final method");
          }
          if (!$is_abstract) {
            throw new ParserException(
              $this->iterator->getSourcePosition(),
              "can not use abstract modifier in non-abstract class");
          }
          $modifiers->abstract = $this->mustMatch(T_ABSTRACT, $modifiers);
          break;
        case T_FUNCTION:
          return $this->classMethod($modifiers);
        case T_VARIABLE:
          return $this->classMemberList($modifiers);
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
   * @param ModifiersNode $modifiers Member modifiers
   * @return ClassMemberListNode
   * @throws ParserException
   */
  private function classMemberList(ModifiersNode $modifiers) {
    // Modifier checks
    if ($modifiers->abstract) {
      throw new ParserException(
        $this->iterator->getSourcePosition(),
        "members can not be declared abstract");
    }
    if ($modifiers->final) {
      throw new ParserException(
        $this->iterator->getSourcePosition(),
        "members can not be declared final");
    }
    $node = new ClassMemberListNode();
    $node->modifiers = $node->appendChild($modifiers);
    do {
      $node->members[] = $node->appendChild($this->classMember());
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a class member.
   * @return ClassMemberNode
   */
  private function classMember() {
    $node = new ClassMemberNode();
    $node->name = $this->mustMatch(T_VARIABLE, $node);
    if ($this->tryMatch('=', $node)) {
      $node->defaultValue = $node->appendChild($this->staticScalar());
    }
    return $node;
  }

  /**
   * Parse a class method
   * @param ModifiersNode $modifiers Method modifiers
   * @return ClassMethodNode
   */
  private function classMethod(ModifiersNode $modifiers) {
    $node = new ClassMethodNode();
    $node->modifiers = $modifiers;
    $node->appendChild($modifiers);
    $this->mustMatch(T_FUNCTION, $node);
    $node->reference = $this->tryMatch('&', $node);
    $node->name = $this->mustMatch(T_STRING, $node);
    $node->parameters = $node->appendChild($this->parameterList());
    if ($node->modifiers->abstract) {
      $this->mustMatch(';', $node);
      return $node;
    }
    $node->body = $node->appendChild($this->innerStatementBlock());
    return $node;
  }

  /**
   * Parse a trait use statement.
   * @return Node
   */
  private function traitUse() {
    $node = new Node();
    $this->mustMatch(T_USE, $node);
    // trait_list
    do {
      $node->appendChild($this->namespacePath());
    } while ($this->tryMatch(',', $node));
    // trait_adaptations
    if ($this->tryMatch('{', $node)) {
      while ($this->iterator->hasNext() && !$this->isTokenType('}')) {
        $node->appendChild($this->traitAdaptation());
      }
      $this->mustMatch('}', $node, TRUE);
      return $node;
    }
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a trait adaptation statement.
   * @return Node
   */
  private function traitAdaptation() {
    $qualified_name = $this->namespacePath();
    if (count($qualified_name->children) === 1) {
      $qualified_name = $qualified_name->children[0];
      return $this->traitAlias($qualified_name);
    }
    $node = new Node();
    $node->appendChild($qualified_name);
    $this->mustMatch(T_DOUBLE_COLON, $node);
    $this->mustMatch(T_STRING, $node);
    if ($this->isTokenType(T_AS)) {
      return $this->traitAlias($node);
    }
    $this->mustMatch(T_INSTEADOF, $node);
    do {
      $node->appendChild($this->namespacePath());
    } while ($this->tryMatch(',', $node));
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse a trait alias.
   * @param Node $trait_method_reference
   * @return Node
   */
  private function traitAlias(Node $trait_method_reference) {
    $node = new Node();
    $node->appendChild($trait_method_reference);
    $this->mustMatch(T_AS, $node);
    if ($trait_modifier = $this->tryMatchToken(T_PUBLIC, T_PROTECTED, T_PRIVATE)) {
      $node->appendChild($trait_modifier);
      $this->tryMatch(T_STRING, $node);
      $this->mustMatch(';', $node, TRUE);
      return $node;
    }
    $this->mustMatch(T_STRING, $node);
    $this->mustMatch(';', $node, TRUE);
    return $node;
  }

  /**
   * Parse an interface declaration.
   * @return Node
   */
  private function interfaceDeclaration() {
    $node = new InterfaceNode();
    $this->mustMatch(T_INTERFACE, $node);
    $node->name = $this->mustMatch(T_STRING, $node);
    if ($this->tryMatch(T_EXTENDS, $node)) {
      do {
        $node->extends[] = $node->appendChild($this->namespacePath());
      } while ($this->tryMatch(',', $node));
    }
    $this->mustMatch('{', $node);
    while (!$this->isTokenType('}')) {
      if ($this->isTokenType(T_CONST)) {
        $node->constants[] = $node->appendChild($this->_const());
      }
      else {
        $node->methods[] = $node->appendChild($this->interfaceMethod());
      }
    }
    $this->mustMatch('}', $node, TRUE);
    return $node;
  }

  /**
   * Parse an interface method declaration.
   * @return InterfaceMethodNode
   * @throws ParserException
   */
  private function interfaceMethod() {
    $node = new InterfaceMethodNode();
    $is_static = $this->tryMatch(T_STATIC, $node);
    while ($this->isTokenType(T_PUBLIC, T_PROTECTED, T_PRIVATE)) {
      if ($node->visibility) {
        throw new ParserException(
          $this->iterator->getSourcePosition(),
          "can only have one visibility modifier on interface method."
        );
      }
      $node->visibility = $this->mustMatch($this->getTokenType(), $node);
    }
    !$is_static && $this->tryMatch(T_STATIC, $node);
    $this->mustMatch(T_FUNCTION, $node);
    $this->tryMatch('&', $node);
    $node->name = $this->mustMatch(T_STRING, $node);
    $node->parameters = $node->appendChild($this->parameterList());
    $this->mustMatch(';', $node);
    return $node;
  }


  /**
   * Parse a trait declaration.
   * @return TraitNode
   */
  private function traitDeclaration() {
    $node = new TraitNode();
    $this->mustMatch(T_TRAIT, $node);
    $node->name = $this->mustMatch(T_STRING, $node);
    if ($this->tryMatch(T_EXTENDS, $node)) {
      $node->extends = $node->appendChild($this->namespacePath());
    }
    if ($this->tryMatch(T_IMPLEMENTS, $node)) {
      do {
        $node->implements[] = $node->appendChild($this->namespacePath());
      } while ($this->tryMatch(',', $node));
    }
    $this->mustMatch('{', $node);
    while (!$this->isTokenType('}')) {
      $node->statements[] = $node->appendChild($this->classStatement(TRUE));
    }
    $this->mustMatch('}', $node, TRUE);
    return $node;
  }

  /**
   * Test if $token_type is a hidden token type.
   * @param $token_type
   * @return bool
   */
  static private function isHidden($token_type) {
    return $token_type === T_WHITESPACE ||
    $token_type === T_COMMENT ||
    $token_type === T_DOC_COMMENT;
  }

  /**
   * Skip hidden tokens.
   */
  private function skipHidden() {
    $token = $this->iterator->current();
    while ($token && self::isHidden($token->type)) {
      $node = new TokenNode($token);
      if ($token->type === T_DOC_COMMENT) {
        $this->docComment = $node;
      }
      $this->skipped[] = $node;
      $token = $this->iterator->next();
    }
  }

  /**
   * Add any previously skipped tokens to $parent.
   * @param Node $parent
   */
  private function addSkipped(Node $parent) {
    foreach ($this->skipped as $node) {
      $parent->appendChild($node);
    }
    $this->skipped = array();
  }

  /**
   * Match hidden tokens and add to $parent.
   * @param Node $parent
   */
  private function matchHidden(Node $parent) {
    $this->skipHidden();
    $this->addSkipped($parent);
  }

  /**
   * @param int $expected_type
   * @param Node $parent
   * @param bool $maybe_last TRUE if this may be the last match for rule.
   * @return TokenNode
   * @throws ParserException
   */
  private function mustMatch($expected_type, Node $parent, $maybe_last = FALSE) {
    $this->skipHidden();
    $token = $this->iterator->current();
    if ($token === NULL || $token->type !== $expected_type) {
      throw new ParserException(
        $this->iterator->getSourcePosition(),
        'expected ' . Token::typeName($expected_type));
    }
    $this->addSkipped($parent);
    $this->docComment = NULL;
    $node = new TokenNode($token);
    $parent->appendChild($node);
    $this->iterator->next();
    if (!$maybe_last) {
      $this->matchHidden($parent);
    }
    return $node;
  }

  /**
   * @param int $expected_type
   * @param Node $parent
   * @param bool $maybe_last TRUE if this may be the last match for rule.
   * @return TokenNode
   */
  private function tryMatch($expected_type, Node $parent, $maybe_last = FALSE) {
    $this->skipHidden();
    $token = $this->iterator->current();
    if ($token === NULL || $token->type !== $expected_type) {
      return NULL;
    }
    $this->addSkipped($parent);
    $this->docComment = NULL;
    $node = new TokenNode($token);
    $parent->appendChild($node);
    $this->iterator->next();
    if (!$maybe_last) {
      $this->matchHidden($parent);
    }
    return $node;
  }

  /**
   * @return TokenNode
   */
  private function tryMatchToken() {
    $token = $this->iterator->current();
    if ($token === NULL) {
      return NULL;
    }
    foreach (func_get_args() as $expected_type) {
      if ($expected_type === $token->type) {
        $this->iterator->next();
        return new TokenNode($token);
      }
    }
    return NULL;
  }

  /**
   * @param $expected_type
   * @return TokenNode
   * @throws ParserException
   */
  private function mustMatchToken($expected_type) {
    $token = $this->iterator->current();
    if ($token === NULL || $token->type !== $expected_type) {
      throw new ParserException(
        $this->iterator->getSourcePosition(),
        'expected ' . Token::typeName($expected_type));
    }
    $this->iterator->next();
    return new TokenNode($token);
  }

  /**
   * Return the current token type.
   * @return mixed
   */
  private function getTokenType() {
    $this->skipHidden();
    $token = $this->iterator->current();
    return $token === NULL ? NULL : $token->type;
  }

  /**
   * Test if current token is one of the types.
   * @return bool
   */
  private function isTokenType() {
    $actual_type = $this->getTokenType();
    foreach (func_get_args() as $expected_type) {
      if ($expected_type === $actual_type) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Mark current position.
   */
  private function mark() {
    $this->iterator->mark();
  }

  /**
   * Rewind back to mark.
   */
  private function rewind() {
    $this->iterator->rewind();
    $this->skipped = array();
    $this->docComment = NULL;
  }
}
