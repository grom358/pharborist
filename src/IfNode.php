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
}
