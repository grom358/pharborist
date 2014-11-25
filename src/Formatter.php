<?php
namespace Pharborist;

use Pharborist\Constants\ConstantNode;
use Pharborist\ControlStructures\DoWhileNode;
use Pharborist\ControlStructures\ElseIfNode;
use Pharborist\ControlStructures\ForeachNode;
use Pharborist\ControlStructures\ForNode;
use Pharborist\ControlStructures\IfNode;
use Pharborist\ControlStructures\SwitchNode;
use Pharborist\ControlStructures\WhileNode;
use Pharborist\Exceptions\CatchNode;
use Pharborist\Exceptions\TryCatchNode;
use Pharborist\Functions\CallNode;
use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Functions\ParameterNode;
use Pharborist\Objects\ClassMethodNode;
use Pharborist\Objects\InterfaceMethodNode;
use Pharborist\Objects\InterfaceNode;
use Pharborist\Objects\NewNode;
use Pharborist\Objects\SingleInheritanceNode;
use Pharborist\Operators\BinaryOperationNode;
use Pharborist\Types\ArrayNode;
use Pharborist\Types\BooleanNode;
use Pharborist\Types\NullNode;

class Formatter extends VisitorBase {
  private $indentLevel = 0;

  /**
   * @param WhitespaceNode|NULL $wsNode
   * @return string
   */
  public function getNewlineIndent($wsNode = NULL) {
    $nl = Settings::get('formatter.nl');
    $indent_per_level = Settings::get('formatter.indent');
    $indent = str_repeat($indent_per_level, $this->indentLevel);
    $nl_count = $wsNode ? $wsNode->getNewlineCount() : 1;
    $nl_count = max($nl_count, 1);
    return str_repeat($nl, $nl_count) . $indent;
  }

  public function spaceBefore(Node $node, $enforce = TRUE) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      if ($enforce) {
        $prev->setText(' ');
      }
    }
    else {
      $node->before(Token::space());
    }
  }

  public function spaceAfter(Node $node, $enforce = TRUE) {
    $next = $node->nextToken();
    if ($next instanceof WhitespaceNode) {
      if ($enforce) {
        $next->setText(' ');
      }
    }
    else {
      $node->after(Token::space());
    }
  }

  public function removeSpaceBefore(Node $node) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      $prev->remove();
    }
  }

  public function removeSpaceAfter(Node $node) {
    $next = $node->nextToken();
    if ($next instanceof WhitespaceNode) {
      $next->remove();
    }
  }

  public function newlineBefore(Node $node) {
    $prev = $node->previousToken();
    if ($prev instanceof WhitespaceNode) {
      $prev->setText($this->getNewlineIndent($prev));
    }
    else {
      $node->before(Token::whitespace($this->getNewlineIndent()));
    }
  }

  public function newlineAfter(Node $node) {
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
   * @param Node|NodeInterface $condition
   */
  protected function formatCondition($condition) {
    $this->removeSpaceBefore($condition);
    $this->removeSpaceAfter($condition);
    $open_paren = $condition->previousUntil(Filter::isTokenType('('), TRUE)->get(0);
    $this->spaceBefore($open_paren);
  }

  public function beginIfNode(IfNode $node) {
    $this->indentLevel++;
    $this->formatCondition($node->getCondition());
    $this->encloseBlock($node->getThen());
    $this->encloseBlock($node->getElse());
    if ($node->getElse()) {
      $elseKeyword = $node->getElse()->previousUntil(Filter::isTokenType(T_ELSE), TRUE)->get(0);
      $this->indentLevel--;
      $this->newlineBefore($elseKeyword);
      $this->indentLevel++;
    }
  }

  public function visitElseIfNode(ElseIfNode $node) {
    $this->formatCondition($node->getCondition());
    $this->encloseBlock($node->getThen());
    $this->indentLevel--;
    $this->newlineBefore($node);
    $this->indentLevel++;
  }

  public function endIfNode(IfNode $node) {
    $this->indentLevel--;
  }

  public function beginWhileNode(WhileNode $node) {
    $this->indentLevel++;
    $this->formatCondition($node->getCondition());
    $this->encloseBlock($node->getBody());
  }

  public function endWhileNode(WhileNode $node) {
    $this->indentLevel--;
  }

  public function beginDoWhileNode(DoWhileNode $node) {
    $this->indentLevel++;
    $this->formatCondition($node->getCondition());
    $this->encloseBlock($node->getBody());
    $this->spaceBefore($node->children(Filter::isTokenType(T_WHILE))->get(0));
  }

  public function endDoWhileNode(DoWhileNode $node) {
    $this->indentLevel--;
  }

  public function beginForNode(ForNode $node) {
    $this->indentLevel++;
    $node->getInitial()->previousUntil(Filter::isTokenType('('))->remove();
    $this->spaceBefore($node->getInitial()->previous());
    $node->getStep()->nextUntil(Filter::isTokenType(')'))->remove();
    $this->encloseBlock($node->getBody());
    $separator = $node->getInitial()->nextUntil(Filter::isTokenType(';'), TRUE)->last()->get(0);
    $this->removeSpaceBefore($separator);
    $this->spaceAfter($separator);
    $separator = $node->getCondition()->nextUntil(Filter::isTokenType(';'), TRUE)->last()->get(0);
    $this->removeSpaceBefore($separator);
    $this->spaceAfter($separator);
  }

  public function endForNode(ForNode $node) {
    $this->indentLevel--;
  }

  public function beginForeachNode(ForeachNode $node) {
    $this->indentLevel++;
    $node->getOnEach()->previousUntil(Filter::isTokenType('('))->remove();
    $this->spaceBefore($node->getOnEach()->previous());
    $node->getValue()->nextUntil(Filter::isTokenType(')'))->remove();
    if ($node->getKey()) {
      $arrow = $node->getKey()->nextUntil(Filter::isTokenType(T_DOUBLE_ARROW), TRUE)->last()->get(0);
      $this->spaceBefore($arrow);
      $this->spaceAfter($arrow);
    }
    $this->encloseBlock($node->getBody());
  }

  public function endForeachNode(ForeachNode $node) {
    $this->indentLevel--;
  }

  public function beginSwitchNode(SwitchNode $node) {
    $this->indentLevel++;
    $this->formatCondition($node->getSwitchOn());

    /** @var TokenNode $token */
    $token = $node->getSwitchOn()->nextUntil(Filter::isTokenType(':', '{'), TRUE)->last()->get(0);
    if ($token->getType() === ':') {
      $this->removeSpaceBefore($token);
    }
    else {
      $this->spaceBefore($token);
    }

    $cases = $node->getCases();

    // Indent before each case.
    foreach ($cases as $case) {
      $this->newlineBefore($case);
    }

    $this->indentLevel--;
    $this->newlineBefore($node->lastChild());
    $this->indentLevel++;

    $this->indentLevel++;
  }

  public function endSwitchNode(SwitchNode $node) {
    $this->indentLevel -= 2;
  }

  public function visitStatementBlockNode(StatementBlockNode $node) {
    $first = $node->firstChild();
    if ($first instanceof TokenNode && $first->getType() === '{') {
      $this->spaceBefore($node);

      // Newline before closing }.
      $this->indentLevel--;
      $this->newlineBefore($node->lastChild());
      $this->indentLevel++;
    }

    foreach ($node->getStatements() as $statement) {
      $this->newlineBefore($statement);
    }
  }

  public function visitExpressionStatementNode(ExpressionStatementNode $node) {
    $nl = Settings::get('formatter.nl');
    $indent_per_level = Settings::get('formatter.indent');
    $indent = str_repeat($indent_per_level, $this->indentLevel + 1);
    $collection = $node->find(Filter::isInstanceOf('\Pharborist\WhitespaceNode'));
    /** @var WhitespaceNode $ws */
    foreach ($collection as $ws) {
      $newline_count = $ws->getNewlineCount();
      if ($newline_count > 0) {
        $ws->setText(str_repeat($nl, $newline_count) . $indent);
      }
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

  public function beginArrayNode(ArrayNode $node) {
    $this->indentLevel++;
  }

  public function endArrayNode(ArrayNode $node) {
    // Remove space after T_ARRAY.
    $first = $node->firstChild();
    /** @var TokenNode $first */
    if ($first->getType() === T_ARRAY) {
      $this->removeSpaceAfter($first);
    }

    // Spaces around => operator.
    $arrows = $node->getElementList()
      ->children(Filter::isInstanceOf('\Pharborist\Types\ArrayPairNode'))
      ->children(Filter::isTokenType(T_DOUBLE_ARROW));
    foreach ($arrows as $arrow) {
      $this->spaceBefore($arrow);
      $this->spaceAfter($arrow);
    }

    /** @var TokenNode[] $commas */
    $commas = $node->getElementList()->children(Filter::isTokenType(','));

    // Remove spaces before , tokens.
    foreach ($commas as $comma) {
      $this->removeSpaceBefore($comma);
    }

    // Line wrap array if required.
    // If already on multiple lines, make array line wrap.
    $multi_line = $node->find(function (Node $node) {
      return $node instanceof WhitespaceNode && $node->getNewlineCount() > 0;
    })->isNotEmpty();
    if (!$multi_line) {
      // Test if array exceeds the soft limit.
      $column_position = $this->calculateColumnPosition($node);
      $column_position += strlen($node->getText());
      $soft_limit = Settings::get('formatter.soft_limit');
      $multi_line = $column_position > $soft_limit;
    }

    if ($multi_line) {
      // Newline before first element.
      $this->newlineBefore($node->getElementList());

      // Newline after each comma.
      foreach ($commas as $comma) {
        $this->newlineAfter($comma);
      }

      // Enforce trailing comma after last element.
      $last = $node->getElementList()->lastChild();
      if (!($last instanceof TokenNode && $last->getType() === ',')) {
        $node->getElementList()->append(Token::comma());
      }

      // Newline before closing ) or ].
      $this->indentLevel--;
      $this->newlineBefore($node->lastChild());
      $this->indentLevel++;
    }
    else {
      // Remove whitespace before first element.
      $this->removeSpaceBefore($node->getElementList());

      // Remove whitespace after last element.
      $this->removeSpaceBefore($node->lastChild());

      // Single space after comma.
      foreach ($commas as $comma) {
        $this->spaceAfter($comma);
      }
    }

    $this->indentLevel--;
  }

  public function beginFunctionDeclarationNode(FunctionDeclarationNode $node) {
    $this->indentLevel++;
    $parameter_list = $node->getParameterList();
    $this->removeSpaceBefore($parameter_list);
    $this->removeSpaceAfter($parameter_list);
    $open_paren = $parameter_list->previousUntil(Filter::isTokenType('('), TRUE)->get(0);
    $this->removeSpaceBefore($open_paren);
  }

  public function endFunctionDeclarationNode(FunctionDeclarationNode $node) {
    $this->indentLevel--;
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

  public function beginCallNode(CallNode $node) {
    $this->indentLevel++;
    $arg_list = $node->getArgumentList();
    $this->removeSpaceBefore($arg_list);
    $this->removeSpaceAfter($arg_list);
    $open_paren = $arg_list->previousUntil(Filter::isTokenType('('), TRUE)->get(0);
    $this->removeSpaceBefore($open_paren);
  }

  public function endCallNode(CallNode $node) {
    $this->indentLevel--;
  }

  /**
   * @param SingleInheritanceNode|InterfaceNode $node
   */
  protected function beginClassTraitOrInterface($node) {
    $close_brace = $node->lastChild();
    $this->newlineBefore($close_brace);
    $this->indentLevel++;
  }

  /**
   * @param SingleInheritanceNode|InterfaceNode $node
   */
  protected function endClassTraitOrInterface($node) {
    /** @var WhitespaceNode $ws_node */
    foreach ($node->getBody()->children(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))->slice(1) as $ws_node) {
      $ws_node->setText("\n" . $ws_node->getText());
    }
    $this->indentLevel--;
  }

  /**
   * @param ClassMethodNode|InterfaceMethodNode $node
   */
  protected function beginMethod($node) {
    $this->indentLevel++;
    $parameter_list = $node->getParameterList();
    $this->removeSpaceBefore($parameter_list);
    $this->removeSpaceAfter($parameter_list);
    $open_paren = $parameter_list->previousUntil(Filter::isTokenType('('), TRUE)->get(0);
    $this->removeSpaceBefore($open_paren);

    if ($node->getVisibility() === NULL) {
      $node->setVisibility('public');
    }
  }

  public function beginSingleInheritanceNode(SingleInheritanceNode $node) {
    $this->beginClassTraitOrInterface($node);
  }

  public function endSingleInheritanceNode(SingleInheritanceNode $node) {
    $this->endClassTraitOrInterface($node);
  }

  public function beginClassMethodNode(ClassMethodNode $node) {
    $close_brace = $node->getBody()->lastChild();
    $this->newlineBefore($close_brace);
    $this->beginMethod($node);
  }

  public function endClassMethodNode(ClassMethodNode $node) {
    $this->indentLevel--;
  }

  public function beginInterfaceNode(InterfaceNode $node) {
    $this->beginClassTraitOrInterface($node);
  }

  public function endInterfaceNode(InterfaceNode $node) {
    $this->endClassTraitOrInterface($node);
  }

  public function beginInterfaceMethodNode(InterfaceMethodNode $node) {
    $this->beginMethod($node);
  }

  public function endInterfaceMethodNode(InterfaceMethodNode $node) {
    $this->indentLevel--;
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

  public function beginTryCatchNode(TryCatchNode $node) {
    $this->newlineAfter($node->getTry());
    $this->indentLevel++;
  }

  public function endTryCatchNode(TryCatchNode $node) {
    $this->indentLevel--;
  }

  public function visitCatchNode(CatchNode $node) {
    $open_paren = $node->getExceptionType()->previousUntil(Filter::isTokenType('('), TRUE)->get(0);
    $this->spaceBefore($open_paren);
    $this->removeSpaceAfter($open_paren);
    $close_paren = $node->getBody()->previousUntil(Filter::isTokenType(')'), TRUE)->get(0);
    $this->removeSpaceBefore($close_paren);
    $this->spaceAfter($close_paren);
  }

  public function visitNewNode(NewNode $node) {
    if (!$node->getArgumentList()) {
      $node->append(Token::openParen());
      $node->addChild(new CommaListNode(), 'arguments');
      $node->append(Token::closeParen());
    }
  }
}
