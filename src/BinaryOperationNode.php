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
  public function getLeftOperand() {
    return $this->left;
  }

  /**
   * @return $this
   */
  public function setLeftOperand(ExpressionNode $expr) {
    $this->left = $expr;
    return $this;
  }

  /**
   * @return Node
   */
  public function getOperator() {
    return $this->operator;
  }
  
  /**
   * @return $this
   */
  public function setOperator(Node $operator) {
    $this->operator = $operator;
    return $this;
  }

  /**
   * @return ExpressionNode
   */
  public function getRightOperand() {
    return $this->right;
  }
  
  /**
   * @return $this
   */
  public function setRightOperand(ExpressionNode $expr) {
    $this->right = $expr;
    return $this;
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->getLeft() . ' ' . $this->getOperator() . ' ' . $this->getRight();
  }
}
