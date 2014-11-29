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
use Pharborist\Objects\ClassMethodNode;
use Pharborist\Objects\InterfaceMethodNode;
use Pharborist\Objects\InterfaceNode;
use Pharborist\Objects\NewNode;
use Pharborist\Objects\ObjectMethodCallNode;
use Pharborist\Objects\SingleInheritanceNode;
use Pharborist\Operators\BinaryOperationNode;
use Pharborist\Operators\CastNode;
use Pharborist\Operators\PostDecrementNode;
use Pharborist\Operators\PostIncrementNode;
use Pharborist\Operators\UnaryOperationNode;
use Pharborist\Types\ArrayNode;
use Pharborist\Types\BooleanNode;
use Pharborist\Types\NullNode;

class Formatter extends VisitorBase {
  private $indentLevel = 0;

  /**
   * @var \SplObjectStorage
   */
  private $objectStorage;

  public function __construct() {
    $this->objectStorage = new \SplObjectStorage();
  }

  /**
   * @param WhitespaceNode|NULL $wsNode
   * @return string
   */
  protected function getNewlineIndent($wsNode = NULL, $close = FALSE) {
    $nl = Settings::get('formatter.nl');
    $indent_per_level = Settings::get('formatter.indent');
    $indent = str_repeat($indent_per_level, $this->indentLevel - ($close ? 1 : 0));
    $nl_count = $wsNode ? $wsNode->getNewlineCount() : 1;
    $nl_count = max($nl_count, 1);
    return str_repeat($nl, $nl_count) . $indent;
  }

  protected function spaceBefore(Node $node) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      $prev->setText(' ');
    }
    else {
      $node->before(Token::space());
    }
  }

  protected function spaceAfter(Node $node) {
    $next = $node->nextToken();
    if ($next instanceof WhitespaceNode) {
      $next->setText(' ');
    }
    else {
      $node->after(Token::space());
    }
  }

  protected function removeSpaceBefore(Node $node) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      $prev->remove();
    }
  }

  protected function removeSpaceAfter(Node $node) {
    $next = $node->nextToken();
    if ($next instanceof WhitespaceNode) {
      $next->remove();
    }
  }

  protected function newlineBefore(Node $node, $close = FALSE) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      $prev->setText($this->getNewlineIndent($prev, $close));
    }
    else {
      $node->before(Token::whitespace($this->getNewlineIndent(NULL, $close)));
    }
  }

  protected function newlineAfter(Node $node) {
    $next = $node->nextToken();
    if ($next instanceof WhitespaceNode) {
      $next->setText($this->getNewlineIndent($next));
    }
    else {
      $node->after(Token::whitespace($this->getNewlineIndent()));
    }
  }

  public function visitBinaryOperationNode(BinaryOperationNode $node) {
    $operator = $node->getOperator();
    $this->spaceBefore($operator);
    $this->spaceAfter($operator);
  }

  public function visitUnaryOperationNode(UnaryOperationNode $node) {
    $operator = $node->getOperator();
    if ($node instanceof PostDecrementNode || $node instanceof PostIncrementNode) {
      $this->removeSpaceBefore($operator);
    }
    elseif ($node instanceof CastNode) {
      $this->spaceAfter($operator);
    }
    else {
      $this->removeSpaceAfter($operator);
    }
  }

  /**
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
      $else_newline = Settings::get('formatter.else_newline');
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
    $else_newline = Settings::get('formatter.else_newline');
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
    $this->spaceBefore($node->children(Filter::isTokenType(T_WHILE))->get(0));
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

  public function visitStatementBlockNode(StatementBlockNode $node) {
    $nested = FALSE;
    if ($node->parent(Filter::isInstanceOf('\Pharborist\StatementBlockNode'))) {
      $this->indentLevel++;
      $nested = TRUE;
    }
    $this->objectStorage[$node] = $nested;
    $first = $node->firstChild();
    if ($first instanceof TokenNode && $first->getType() === '{') {
      $this->spaceBefore($node);
      $this->newlineAfter($first);
    }

    foreach ($node->getStatements() as $statement) {
      $this->newlineBefore($statement);
    }
  }

  public function endStatementBlockNode(StatementBlockNode $node) {
    $nested = $this->objectStorage[$node];
    unset($this->objectStorage[$node]);
    if ($nested) {
      $this->indentLevel--;
    }
    $last = $node->lastChild();
    if ($last instanceof TokenNode && $last->getType() === '}') {
      $this->newlineBefore($last, TRUE);
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
    $nl = Settings::get('formatter.nl');
    // Add tokens until have whitespace containing newline.
    $column_position = 1;
    $start_token = $node instanceof ParentNode ? $node->firstToken() : $node;
    $token = $start_token;
    while ($token = $token->previousToken()) {
      if ($token instanceof WhitespaceNode && $token->getNewlineCount() > 0) {
        $lines = explode($nl, $token->getText());
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
    if ($node->closest(Filter::isInstanceOf('\Pharborist\Types\ArrayNode'))) {
      $this->indentLevel++;
      $nested = TRUE;
    }

    if (Settings::get('formatter.force_array_new_style')) {
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
      $node->getElementList()->append(Token::comma());
    }

    /** @var NodeCollection|TokenNode[] $commas */
    $commas = $node->getElementList()->children(Filter::isTokenType(','));

    // If already on multiple lines, make array line wrap.
    $multi_line = $commas->next(function (Node $node) {
      return $node instanceof WhitespaceNode && $node->getNewlineCount() > 0;
    })->isNotEmpty();

    $this->objectStorage[$node] = array($multi_line, $nested);
  }

  public function endArrayNode(ArrayNode $node) {
    list($multi_line, $nested) = $this->objectStorage[$node];
    unset($this->objectStorage[$node]);

    if ($nested) {
      $this->indentLevel--;
    }

    if (!$multi_line) {
      // If array exceeds the soft limit then force line wrapping.
      $column_position = $this->calculateColumnPosition($node);
      $column_position += strlen($node->getText());
      $soft_limit = Settings::get('formatter.soft_limit');
      $multi_line = $column_position > $soft_limit;
    }

    if ($multi_line) {
      // Newline before first element.
      $this->newlineBefore($node->getElementList());

      /** @var NodeCollection|TokenNode[] $commas */
      $commas = $node->getElementList()->children(Filter::isTokenType(','));

      // Newline after each comma.
      foreach ($commas as $comma) {
        $this->newlineAfter($comma);
      }

      // Enforce trailing comma after last element.
      $node->getElementList()->append(Token::comma());

      // Newline before closing ) or ].
      $this->newlineBefore($node->lastChild(), TRUE);
    }
  }

  public function visitFunctionDeclarationNode(FunctionDeclarationNode $node) {
    $parameter_list = $node->getParameterList();
    $this->removeSpaceBefore($parameter_list);
    $this->removeSpaceAfter($parameter_list);
    $open_paren = $parameter_list->previousUntil(Filter::isTokenType('('), TRUE)->get(0);
    $this->removeSpaceBefore($open_paren);
  }

  public function visitParameterNode(ParameterNode $node) {
    if ($node->getValue()) {
      $assign = $node->getValue()->previousUntil(Filter::isTokenType('='), TRUE)->get(0);
      $this->spaceBefore($assign);
      $this->spaceAfter($assign);
    }
  }

  public function visitCommaListNode(CommaListNode $node) {
    foreach ($node->children(Filter::isTokenType(',')) as $comma_node) {
      $this->removeSpaceBefore($comma_node);
      $this->spaceAfter($comma_node);
    }
  }

  public function visitCallNode(CallNode $node) {
    $arg_list = $node->getArgumentList();
    $this->removeSpaceBefore($arg_list);
    $this->removeSpaceAfter($arg_list);
    $open_paren = $arg_list->previousUntil(Filter::isTokenType('('), TRUE)->get(0);
    $this->removeSpaceBefore($open_paren);
  }

  /**
   * @param SingleInheritanceNode|InterfaceNode $node
   */
  protected function endClassTraitOrInterface($node) {
    $nl = Settings::get('formatter.nl');
    $indent = Settings::get('formatter.indent');
    /** @var WhitespaceNode $ws_node */
    $whitespace = $node->getBody()->children(Filter::isInstanceOf('\Pharborist\WhitespaceNode'));
    foreach ($whitespace->slice(1, -1) as $ws_node) {
      $ws_node->setText(str_repeat($nl, 2) . $indent);
    }
  }

  /**
   * @param ClassMethodNode|InterfaceMethodNode $node
   */
  protected function visitMethod($node) {
    $parameter_list = $node->getParameterList();
    $this->removeSpaceBefore($parameter_list);
    $this->removeSpaceAfter($parameter_list);
    $open_paren = $parameter_list->previousUntil(Filter::isTokenType('('), TRUE)->get(0);
    $this->removeSpaceBefore($open_paren);

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

  public function endSingleInheritanceNode(SingleInheritanceNode $node) {
    $this->endClassTraitOrInterface($node);
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

  public function endInterfaceNode(InterfaceNode $node) {
    $this->endClassTraitOrInterface($node);
  }

  public function visitInterfaceMethodNode(InterfaceMethodNode $node) {
    $this->visitMethod($node);
  }

  protected function handleBuiltinConstantNode(ConstantNode $node) {
    $to_upper = Settings::get('formatter.boolean_null.upper');
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
    $object_operator = $node->getMethodName()->previousUntil(Filter::isTokenType(T_OBJECT_OPERATOR), TRUE)->get(0);
    $this->removeSpaceAfter($object_operator);
  }

  public function visitWhitespaceNode(WhitespaceNode $node) {
    // Normalise whitespace.
    $nl_count = $node->getNewlineCount();
    if ($nl_count > 0) {
      $node->setText($this->getNewlineIndent($node));
    }
    else {
      $node->setText(' ');
    }
  }

  public function visitStatementNode(StatementNode $node) {
    $this->indentLevel++;
  }

  public function endStatementNode(StatementNode $node) {
    $this->indentLevel--;
  }

  public function visitTokenNode(TokenNode $node) {
    switch ($node->getType()) {
      case T_DOUBLE_ARROW:
        $this->spaceBefore($node);
        $this->spaceAfter($node);
        break;
    }
  }
}
