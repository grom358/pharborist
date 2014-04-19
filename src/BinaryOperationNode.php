<?php
namespace Pharborist;

/**
 * A binary operation.
 */
abstract class BinaryOperationNode extends ParentNode implements ExpressionNode {
  /**
   * @var ExpressionNode
   */
  protected $left;

  /**
   * @var Node
   */
  protected $operator;

  /**
   * @var ExpressionNode
   */
  protected $right;

  /**
   * @return ExpressionNode
   */
  public function getLeft() {
    return $this->left;
  }

  /**
   * @return Node
   */
  public function getOperator() {
    return $this->operator;
  }

  /**
   * @return ExpressionNode
   */
  public function getRight() {
    return $this->right;
  }
}
