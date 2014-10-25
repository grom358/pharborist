<?php
namespace Pharborist;

/**
 * An if control structure.
 */
class IfNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @var Node
   */
  protected $then;

  /**
   * @var TokenNode
   */
  protected $elseKeyword;

  /**
   * @var Node
   */
  protected $else;

  /**
   * @return ExpressionNode
   */
  public function getCondition() {
    return $this->condition;
  }

  /**
   * @return Node
   */
  public function getThen() {
    return $this->then;
  }

  /**
   * @return ElseIfNode[]
   */
  public function getElseIfs() {
    return $this->childrenByInstance('\Pharborist\ElseIfNode');
  }

  /**
   * @return Node
   */
  public function getElse() {
    return $this->else;
  }

  public function indent($indent, $level = 0) {
    parent::indent($indent, $level);
    foreach ($this->getElseIfs() as $else_if) {
      $else_if_keyword = $else_if->firstToken();
      $prev = $else_if_keyword->previousToken();
      if ($prev instanceof WhitespaceNode) {
        $prev->indent($indent, $level);
      }
    }
    if ($this->elseKeyword) {
      $prev = $this->elseKeyword->previousToken();
      if ($prev instanceof WhitespaceNode) {
        $prev->indent($indent, $level);
      }
    }
    return $this;
  }
}
