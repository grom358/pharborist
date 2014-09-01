<?php
namespace Pharborist\Operator;

use Pharborist\ExpressionNode;
use Pharborist\ParentNode;

/**
 * A ternary operation.
 *
 * For example, $condition ? $then : $else
 */
class TernaryOperationNode extends ParentNode implements ExpressionNode {
  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @var ExpressionNode
   */
  protected $then;

  /**
   * @var ExpressionNode
   */
  protected $else;

  /**
   * @return ExpressionNode
   */
  public function getCondition() {
    return $this->condition;
  }

  /**
   * @return ExpressionNode
   */
  public function getThen() {
    return $this->then;
  }

  /**
   * @return ExpressionNode
   */
  public function getElse() {
    return $this->else;
  }
}
