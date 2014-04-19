<?php
namespace Pharborist;

/**
 * elseif control structure.
 */
class ElseIfNode extends ParentNode {
  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @var Node
   */
  protected $then;

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
}
