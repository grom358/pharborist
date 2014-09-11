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
   * @param ExpressionNode $condition
   *
   * @return $this
   */
  public function setCondition(ExpressionNode $condition) {
    $this->condition->replaceWith($condition);
    return $this;
  }

  /**
   * @return ExpressionNode
   */
  public function getThen() {
    return $this->then;
  }

  /**
   * @param ExpressionNode $then
   *
   * @return $this
   */
  public function setThen(ExpressionNode $then) {
    $this->then->replaceWith($then);
    return $this;
  }

  /**
   * @return ExpressionNode
   */
  public function getElse() {
    return $this->else;
  }

  /**
   * @param ExpressionNode $else
   *
   * @return $this
   */
  public function setElse(ExpressionNode $else) {
    $this->condition->replaceWith($else);
    return $this;
  }
}
