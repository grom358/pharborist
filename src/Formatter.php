<?php
namespace Pharborist;

use Pharborist\Constants\ConstantNode;
use Pharborist\ControlStructures\CaseNode;
use Pharborist\ControlStructures\DefaultNode;
use Pharborist\ControlStructures\DoWhileNode;
use Pharborist\ControlStructures\ElseIfNode;
use Pharborist\ControlStructures\ForeachNode;
use Pharborist\ControlStructures\ForNode;
use Pharborist\ControlStructures\IfNode;
use Pharborist\ControlStructures\SwitchNode;
use Pharborist\ControlStructures\WhileNode;
use Pharborist\Exceptions\CatchNode;
use Pharborist\Functions\CallNode;
use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Functions\ParameterNode;
use Pharborist\Namespaces\NamespaceNode;
use Pharborist\Objects\ClassMethodNode;
use Pharborist\Objects\InterfaceMethodNode;
use Pharborist\Objects\InterfaceNode;
use Pharborist\Objects\NewNode;
use Pharborist\Objects\ObjectMethodCallNode;
use Pharborist\Objects\SingleInheritanceNode;
use Pharborist\Operators\BinaryOperationNode;
use Pharborist\Operators\CastNode;
use Pharborist\Operators\CloneNode;
use Pharborist\Operators\PostDecrementNode;
use Pharborist\Operators\PostIncrementNode;
use Pharborist\Operators\PrintNode;
use Pharborist\Operators\UnaryOperationNode;
use Pharborist\Types\ArrayNode;
use Pharborist\Types\BooleanNode;
use Pharborist\Types\NullNode;

class Formatter extends VisitorBase {
  /**
   * Formatter config
   *
   * @var array
   */
  protected $config;

  /**
   * Current indentation level.
   *
   * @var int
   */
  protected $indentLevel = 0;

  /**
   * Data attached to nodes.
   *
   * @var \SplObjectStorage
   */
  protected $nodeData;

  public function __construct($config = []) {
    $this->nodeData = new \SplObjectStorage();
    $this->config = $config + [
      'nl' => "\n",
      'indent' => 2,
      'soft_limit' => 80,
      'boolean_null_upper' => TRUE,
      'force_array_new_style' => TRUE,
      'else_newline' => TRUE,
      'declaration_brace_newline' => FALSE,
      'list_keep_wrap' => FALSE,
      'list_wrap_if_long' => FALSE,
      'blank_lines_around_class_body' => 1,
    ];
  }

  /**
   * @param Node $node
   */
  public function format(Node $node) {
    if ($node instanceof ParentNode) {
      $node->acceptVisitor($this);
    }
    else {
      $this->visit($node);
    }
  }

  /**
   * Get the config value for the specified key.
   *
   * @param string $key
   *   The config key.
   *
   * @return mixed
   */
  public function getConfig($key) {
    return $this->config[$key];
  }

  /**
   * @param bool $close
   *
   * @return string
   */
  protected function getIndent($close = FALSE) {
    $indent_per_level = str_repeat(' ', $this->config['indent']);
    return str_repeat($indent_per_level, $this->indentLevel - ($close ? 1 : 0));
  }

  /**
   * @param WhitespaceNode|NULL $wsNode
   * @param bool $close
   *
   * @return string
   */
  protected function getNewlineIndent($wsNode = NULL, $close = FALSE) {
    $indent = $this->getIndent($close);
    $nl_count = $wsNode ? $wsNode->getNewlineCount() : 1;
    $nl_count = max($nl_count, 1);
    return str_repeat($this->config['nl'], $nl_count) . $indent;
  }

  /**
   * Set a single space before a node.
   *
   * @param Node $node
   *   Node to set space before.
   */
  protected function spaceBefore(Node $node) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      $prev->setText(' ');
    }
    else {
      $node->before(Token::space());
    }
  }

  /**
   * Set a single space after a node.
   *
   * @param Node $node
   *   Node to set space after.
   */
  protected function spaceAfter(Node $node) {
    $next = $node->nextToken();
    if ($next instanceof WhitespaceNode) {
      $next->setText(' ');
    }
    else {
      $node->after(Token::space());
    }
  }

  /**
   * Remove whitespace before a node.
   *
   * @param Node $node
   *   Node to remove space before.
   */
  protected function removeSpaceBefore(Node $node) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      $prev->remove();
    }
  }

  /**
   * Remove whitespace after a node.
   *
   * @param Node $node
   *   Node to remove space after.
   */
  protected function removeSpaceAfter(Node $node) {
    $next = $node->nextToken();
    if ($next instanceof WhitespaceNode) {
      $next->remove();
    }
  }

  /**
   * Set so there a newline before a node.
   *
   * @param Node $node
   *   Node to set newline before.
   * @param bool $close
   *   If the newline is for before a closing token, eg. ) or }
   */
  protected function newlineBefore(Node $node, $close = FALSE) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      $prev_ws = $prev->previousToken();
      if ($prev_ws instanceof CommentNode && $prev_ws->isLineComment() && $prev->getNewlineCount() === 0) {
        $prev->setText($this->getIndent($close));
      }
      else {
        $prev->setText($this->getNewlineIndent($prev, $close));
      }
    }
    else {
      if ($prev instanceof CommentNode && $prev->isLineComment()) {
        if ($this->indentLevel > 0) {
          $node->before(Token::whitespace($this->getIndent($close)));
        }
      }
      else {
        $node->before(Token::whitespace($this->getNewlineIndent(NULL, $close)));
      }
    }
  }

  /**
   * Set so there a newline after a node.
   *
   * @param Node $node
   *   Node to set newline after.
   */
  protected function newlineAfter(Node $node) {
    $next = $node->nextToken();
    if ($next instanceof WhitespaceNode) {
      $next->setText($this->getNewlineIndent($next));
    }
    else {
      $node->after(Token::whitespace($this->getNewlineIndent()));
    }
  }

  public function visitStatementNode(StatementNode $node) {
    $this->indentLevel++;
  }

  public function endStatementNode(StatementNode $node) {
    $this->indentLevel--;
  }

  public function visitBinaryOperationNode(BinaryOperationNode $node) {
    // Space around operator.
    $operator = $node->getOperator();
    $this->spaceBefore($operator);
    // @todo The following results in expressions are single line.
    $this->spaceAfter($operator);
  }

  public function visitUnaryOperationNode(UnaryOperationNode $node) {
    $operator = $node->getOperator();
    if ($node instanceof PostDecrementNode || $node instanceof PostIncrementNode) {
      $this->removeSpaceBefore($operator);
    }
    elseif ($node instanceof CastNode || $node instanceof CloneNode || $node instanceof PrintNode) {
      $this->spaceAfter($operator);
    }
    else {
      $this->removeSpaceAfter($operator);
    }
  }

  public function visitDocCommentNode(DocCommentNode $node) {
    $this->removeSpaceAfter($node);
    if ($this->indentLevel > 0) {
      $this->indentLevel--;
      $node->setIndent($this->getIndent());
      $this->newlineAfter($node);
      $this->indentLevel++;
    }
    else {
      $node->setIndent($this->getIndent());
      $this->newlineAfter($node);
    }
  }

  public function visitWhitespaceNode(WhitespaceNode $node) {
    if ($node->previousToken()->getType() === T_DOC_COMMENT) {
      return;
    }

    // Normalise whitespace.
    $nl_count = $node->getNewlineCount();
    if ($nl_count > 0) {
      $node->setText($this->getNewlineIndent($node));
    }
    else {
      $prev = $node->previousToken();
      if ($prev instanceof CommentNode && $prev->isLineComment()) {
        // Whitespace has already been processed.
      }
      else {
        $node->setText(' ');
      }
    }
  }

  public function visitTokenNode(TokenNode $node) {
    switch ($node->getType()) {
      case T_DOUBLE_ARROW:
        $this->spaceBefore($node);
        $this->spaceAfter($node);
        break;
    }
  }

  /**
   * Handle formatting of constant node.
   *
   * @param ConstantNode $node
   *   true, false, or null node.
   */
  protected function handleBuiltinConstantNode(ConstantNode $node) {
    $to_upper = $this->config['boolean_null_upper'];
    if ($to_upper) {
      $node->toUpperCase();
    }
    else {
      $node->toLowerCase();
    }
  }

  public function visitBooleanNode(BooleanNode $node) {
    $this->handleBuiltinConstantNode($node);
  }

  public function visitNullNode(NullNode $node) {
    $this->handleBuiltinConstantNode($node);
  }

  /**
   * Wrap single line body statements in braces.
   *
   * @param Node|NULL $node
   */
  protected function encloseBlock($node) {
    if ($node && !($node instanceof StatementBlockNode)) {
      $blockNode = new StatementBlockNode();
      $blockNode->append([Token::openBrace(), clone $node, Token::closeBrace()]);
      $node->replaceWith($blockNode);
    }
  }

  /**
   * Handle whitespace around and inside parens for control structures.
   *
   * @param IfNode|ElseIfNode|ForNode|ForeachNode|SwitchNode|DoWhileNode|WhileNode $node
   */
  protected function handleParens($node) {
    $open_paren = $node->getOpenParen();
    $this->removeSpaceAfter($open_paren);
    $this->spaceBefore($open_paren);
    $close_paren = $node->getCloseParen();
    $this->removeSpaceBefore($close_paren);
  }

  /**
   * Generic formatting rules for control structures.
   *
   * @param IfNode|ForNode|ForeachNode|SwitchNode|DoWhileNode|WhileNode $node
   */
  protected function handleControlStructure($node) {
    $this->handleParens($node);
    $colons = $node->children(Filter::isTokenType(':'));
    foreach ($colons as $colon) {
      $this->removeSpaceBefore($colon);
    }
    if ($colons->isNotEmpty()) {
      $this->newlineBefore($node->lastChild()->previous());
    }
  }

  public function visitIfNode(IfNode $node) {
    $this->encloseBlock($node->getThen());
    $this->encloseBlock($node->getElse());
  }

  public function endIfNode(IfNode $node) {
    $this->handleControlStructure($node);
    if ($node->getElse()) {
      $elseKeyword = $node->getElseKeyword();
      $else_newline = $this->config['else_newline'];
      if ($node->isAlterativeSyntax() || $else_newline) {
        $this->newlineBefore($elseKeyword);
      }
      else {
        $this->spaceBefore($elseKeyword);
      }
    }
  }

  public function visitElseIfNode(ElseIfNode $node) {
    $this->handleParens($node);
    $this->encloseBlock($node->getThen());
    $else_newline = $this->config['else_newline'];
    if ($node->getOpenColon() || $else_newline) {
      $this->newlineBefore($node, TRUE);
    }
    else {
      $this->spaceBefore($node);
    }
    if ($colon = $node->getOpenColon()) {
      $this->removeSpaceBefore($colon);
    }
  }

  public function visitWhileNode(WhileNode $node) {
    $this->encloseBlock($node->getBody());
  }

  public function endWhileNode(WhileNode $node) {
    $this->handleControlStructure($node);
  }

  public function visitDoWhileNode(DoWhileNode $node) {
    $this->handleParens($node);
    $this->encloseBlock($node->getBody());
    $this->spaceBefore($node->getWhileKeyword());
  }

  public function visitForNode(ForNode $node) {
    $this->encloseBlock($node->getBody());
    foreach ($node->children(Filter::isTokenType(';')) as $semicolon) {
      $this->removeSpaceBefore($semicolon);
      $this->spaceAfter($semicolon);
    }
  }

  public function endForNode(ForNode $node) {
    $this->handleControlStructure($node);
  }

  public function visitForeachNode(ForeachNode $node) {
    $this->encloseBlock($node->getBody());
  }

  public function endForeachNode(ForeachNode $node) {
    $this->handleControlStructure($node);
  }

  public function endSwitchNode(SwitchNode $node) {
    $this->handleControlStructure($node);

    /** @var TokenNode $token */
    $token = $node->getSwitchOn()->nextUntil(Filter::isTokenType(':', '{'), TRUE)->last()->get(0);
    if ($token->getType() === ':') {
      $this->removeSpaceBefore($token);
    }
    else {
      $this->spaceBefore($token);
    }

    $last = $node->lastChild();
    if ($last instanceof TokenNode && $last->getType() === '}') {
      $this->newlineBefore($last);
    }
  }

  public function endCaseNode(CaseNode $node) {
    $this->newlineBefore($node);
  }

  public function endDefaultNode(DefaultNode $node) {
    $this->newlineBefore($node);
  }

  /**
   * Test if declaration_brace_newline setting applies to node.
   *
   * @param ParentNode $node
   *   Node to test.
   *
   * @return bool
   *   TRUE if declaration_brace_newline applies to node.
   */
  protected function isDeclaration(ParentNode $node) {
    return $node instanceof FunctionDeclarationNode ||
      $node instanceof SingleInheritanceNode ||
      $node instanceof InterfaceNode ||
      $node instanceof ClassMethodNode ||
      $node instanceof InterfaceMethodNode;
  }

  public function visitStatementBlockNode(StatementBlockNode $node) {
    $nested = FALSE;
    $first = $node->firstChild();
    if ($first instanceof TokenNode && $first->getType() === '{') {
      if ($node->parent() instanceof StatementBlockNode) {
        $this->indentLevel++;
        $nested = TRUE;
      }

      $brace_newline = $this->config['declaration_brace_newline'];
      if ($brace_newline && $this->isDeclaration($node->parent())) {
        $this->newlineBefore($node, TRUE);
      }
      else {
        $this->spaceBefore($node);
      }
      $this->newlineAfter($first);
    }
    $this->nodeData[$node] = $nested;

    foreach ($node->getStatements() as $statement) {
      $this->newlineBefore($statement);
    }
  }

  public function endStatementBlockNode(StatementBlockNode $node) {
    $nested = $this->nodeData[$node];
    unset($this->nodeData[$node]);
    if ($nested) {
      $this->indentLevel--;
    }
    $last = $node->lastChild();
    if ($last instanceof TokenNode && $last->getType() === '}') {
      $this->newlineBefore($last, TRUE);
    }
  }

  public function visitLineCommentBlockNode(LineCommentBlockNode $node) {
    if ($this->indentLevel > 0) {
      $indent = $this->getIndent();
      foreach ($node->children(Filter::isInstanceOf('\Pharborist\CommentNode'))->slice(1) as $line_comment) {
        $prev = $line_comment->previous();
        if ($prev instanceof WhitespaceNode) {
          $prev->setText($indent);
        }
        else {
          $line_comment->before(Token::whitespace($indent));
        }
      }
    }
    else {
      $node->children(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))->remove();
    }
  }

  /**
   * Calculate the column start position of the node.
   *
   * @param Node $node
   *   Node to calculate column position for.
   *
   * @return int
   *   Column position.
   */
  protected function calculateColumnPosition(Node $node) {
    // Add tokens until have whitespace containing newline.
    $column_position = 1;
    $start_token = $node instanceof ParentNode ? $node->firstToken() : $node;
    $token = $start_token;
    while ($token = $token->previousToken()) {
      if ($token instanceof WhitespaceNode && $token->getNewlineCount() > 0) {
        $lines = explode($this->config['nl'], $token->getText());
        $last_line = end($lines);
        $column_position += strlen($last_line);
        break;
      }
      $column_position += strlen($token->getText());
    }
    return $column_position;
  }

  public function visitArrayNode(ArrayNode $node) {
    $nested = FALSE;
    if ($node->parents(Filter::isInstanceOf('\Pharborist\Types\ArrayNode'))->isNotEmpty()) {
      $this->indentLevel++;
      $nested = TRUE;
    }

    if ($this->config['force_array_new_style']) {
      $first = $node->firstChild();
      /** @var TokenNode $first */
      if ($first->getType() === T_ARRAY) {
        $open_paren = $first->nextUntil(Filter::isTokenType('('), TRUE)->last()->get(0);
        $open_paren->previousAll()->remove();
        $open_paren->replaceWith(Token::openBracket());
        $close_paren = $node->lastChild();
        $close_paren->replaceWith(Token::closeBracket());
      }
    }

    // Remove space after T_ARRAY.
    $first = $node->firstChild();
    /** @var TokenNode $first */
    if ($first->getType() === T_ARRAY) {
      $this->removeSpaceAfter($first);
    }

    // Remove whitespace before first element.
    $this->removeSpaceBefore($node->getElementList());

    // Remove whitespace after last element.
    $this->removeSpaceAfter($node->getElementList());

    // Remove trailing comma.
    $last = $node->getElementList()->lastChild();
    if ($last instanceof TokenNode && $last->getType() === ',') {
      $last->remove();
    }

    $this->nodeData[$node] = $nested;
  }

  public function endArrayNode(ArrayNode $node) {
    $nested = $this->nodeData[$node];
    unset($this->nodeData[$node]);

    if ($nested) {
      $this->indentLevel--;
    }

    $is_wrapped = $node->getElementList()->children(Filter::isNewline())->isNotEmpty();
    if ($is_wrapped) {
      // Enforce trailing comma after last element.
      $node->getElementList()->append(Token::comma());

      // Newline before closing ) or ].
      $this->newlineBefore($node->lastChild(), !$nested);
    }
  }

  public function visitCommaListNode(CommaListNode $node) {
    if ($node->isEmpty()) {
      return;
    }
    $keep_wrap = $this->config['list_keep_wrap'];
    if (!$keep_wrap) {
      $keep_wrap = $node->parent() instanceof ArrayNode;
    }
    if ($keep_wrap) {
      $has_wrap = $node->children(Filter::isNewline())->isNotEmpty();
      $this->nodeData[$node] = $has_wrap;
    }
    foreach ($node->children(Filter::isTokenType(',')) as $comma_node) {
      $this->removeSpaceBefore($comma_node);
      $this->spaceAfter($comma_node);
    }
  }

  public function endCommaListNode(CommaListNode $node) {
    if ($node->isEmpty()) {
      return;
    }
    $keep_wrap = $this->config['list_keep_wrap'];
    $wrap_if_long = $this->config['list_wrap_if_long'];
    if ($node->parent() instanceof ArrayNode) {
      $keep_wrap = TRUE;
      $wrap_if_long = TRUE;
    }
    $wrap_list = FALSE;
    if ($keep_wrap) {
      $wrap_list = $this->nodeData[$node];
      unset($this->nodeData[$node]);
    }
    if (!$wrap_list && $wrap_if_long) {
      $column_position = $this->calculateColumnPosition($node);
      $column_position += strlen($node->getText());
      $soft_limit = $this->config['soft_limit'];
      $wrap_list = $column_position > $soft_limit;
    }
    if ($wrap_list) {
      $this->newlineBefore($node);
      foreach ($node->children(Filter::isTokenType(',')) as $comma_node) {
        $this->newlineAfter($comma_node);
      }
      $this->newlineAfter($node, TRUE);
    }
  }

  /**
   * @param FunctionDeclarationNode|ClassMethodNode|InterfaceMethodNode $node
   */
  protected function handleParameters($node) {
    $parameter_list = $node->getParameterList();
    $this->removeSpaceBefore($parameter_list);
    $this->removeSpaceAfter($parameter_list);
    $this->removeSpaceBefore($node->getOpenParen());
  }

  public function visitFunctionDeclarationNode(FunctionDeclarationNode $node) {
    $this->handleParameters($node);
  }

  /**
   * @param FunctionDeclarationNode|ClassMethodNode|InterfaceMethodNode $node
   */
  protected function handleParameterWrapping($node) {
    $parameter_list = $node->getParameterList();
    $parameter_wrapped = $parameter_list->children(Filter::isNewline())->isNotEmpty();
    if ($parameter_wrapped) {
      $this->newlineAfter($parameter_list);
      if (!($node instanceof InterfaceMethodNode) && $node->getBody()) {
        $this->spaceBefore($node->getBody());
      }
    }
  }

  public function endFunctionDeclarationNode(FunctionDeclarationNode $node) {
    $this->handleParameterWrapping($node);
  }

  public function visitParameterNode(ParameterNode $node) {
    if ($node->getValue()) {
      $assign = $node->getValue()->previousUntil(Filter::isTokenType('='), TRUE)->get(0);
      $this->spaceBefore($assign);
      $this->spaceAfter($assign);
    }
  }

  public function visitCallNode(CallNode $node) {
    $arg_list = $node->getArgumentList();
    $this->removeSpaceBefore($arg_list);
    $this->removeSpaceAfter($arg_list);
    $this->removeSpaceBefore($node->getOpenParen());
  }

  /**
   * @param SingleInheritanceNode|InterfaceNode $node
   */
  protected function endClassTraitOrInterface($node) {
    $nl = $this->config['nl'];
    $indent = str_repeat(' ', $this->config['indent']);
    $indent = str_repeat($indent, $this->indentLevel + 1);
    /** @var WhitespaceNode $ws_node */
    $whitespace = $node->getBody()->children(Filter::isInstanceOf('\Pharborist\WhitespaceNode'));
    foreach ($whitespace->slice(1, -1) as $ws_node) {
      // Blank line between methods and properties.
      $ws_node->setText(str_repeat($nl, 2) . $indent);
    }

    if ($whitespace->count() === 1) {
      return;
    }

    $blank_lines_around_class_body = $this->config['blank_lines_around_class_body'];
    $nl_count = $blank_lines_around_class_body + 1;

    /** @var WhitespaceNode $open_whitespace */
    $open_whitespace = $whitespace->get(0);
    $open_whitespace->setText(str_repeat($nl, $nl_count) . $indent);

    /** @var WhitespaceNode $close_whitespace */
    $close_whitespace = $whitespace->last()->get(0);
    $indent = str_repeat($indent, $this->indentLevel);
    $close_whitespace->setText(str_repeat($nl, $nl_count) . $indent);
  }

  public function endSingleInheritanceNode(SingleInheritanceNode $node) {
    $this->endClassTraitOrInterface($node);
  }

  /**
   * @param ClassMethodNode|InterfaceMethodNode $node
   */
  protected function visitMethod($node) {
    $this->handleParameters($node);

    if ($node->getVisibility() === NULL) {
      $node->setVisibility('public');
    }
    if ($node->getStatic()) {
      /** @var TokenNode $next */
      $next = $node->getStatic()->nextUntil(Filter::isNotHidden(), TRUE)->last()->get(0);
      if ($next->getType() !== T_FUNCTION) {
        $node->getStatic()->swapWith($node->getVisibility());
      }
    }
  }

  public function visitClassMethodNode(ClassMethodNode $node) {
    if ($node->getBody()) {
      $close_brace = $node->getBody()->lastChild();
      $this->newlineBefore($close_brace);
    }
    $this->visitMethod($node);
    if ($node->getAbstract() && $node->firstChild() !== $node->getAbstract()) {
      $node->getAbstract()->swapWith($node->getVisibility());
    }
    if ($node->getFinal() && $node->firstChild() !== $node->getFinal()) {
      $node->getFinal()->swapWith($node->getVisibility());
    }
  }

  public function endClassMethodNode(ClassMethodNode $node) {
    $this->handleParameterWrapping($node);
  }

  public function endInterfaceNode(InterfaceNode $node) {
    $this->endClassTraitOrInterface($node);
  }

  public function visitInterfaceMethodNode(InterfaceMethodNode $node) {
    $this->visitMethod($node);
  }

  public function endInterfaceMethodNode(InterfaceMethodNode $node) {
    $this->handleParameterWrapping($node);
  }

  public function endCatchNode(CatchNode $node) {
    $this->handleParens($node);
    $this->newlineBefore($node, TRUE);
  }

  public function visitNewNode(NewNode $node) {
    if (!$node->getArgumentList()) {
      $node->append(Token::openParen());
      $node->addChild(new CommaListNode(), 'arguments');
      $node->append(Token::closeParen());
    }
  }

  public function visitObjectMethodCallNode(ObjectMethodCallNode $node) {
    $this->removeSpaceAfter($node->getOperator());
  }

  public function endRootNode(RootNode $node) {
    /** @var $open_tag TokenNode */
    foreach ($node->children(Filter::isTokenType(T_OPEN_TAG)) as $open_tag) {
      $this->removeSpaceAfter($open_tag);
      if ($open_tag !== "<?php\n") {
        $open_tag->setText("<?php\n");
      }
    }
  }

  public function visitNamespaceNode(NamespaceNode $node) {
    $first = $node->getBody()->firstToken();
    $has_braces = $first->getType() === '{';
    $this->indentLevel = $has_braces ? 1 : 0;
    if (!$has_braces) {
      foreach ($node->children(Filter::isTokenType(';')) as $semicolon) {
        $next = $semicolon->next();
        $newlines = str_repeat($this->config['nl'], 2);
        if ($next instanceof WhitespaceNode) {
          $next->setText($newlines);
        }
        else {
          $semicolon->after(Token::whitespace($newlines));
        }
      }
    }
  }

  public function endNamespaceNode(NamespaceNode $node) {
    $this->indentLevel = 0;
  }
}
