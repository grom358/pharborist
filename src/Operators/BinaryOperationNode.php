<?php
namespace Pharborist\Operators;

use Pharborist\ExpressionNode;
use Pharborist\Node;
use Pharborist\ParentNode;

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
   * @param ExpressionNode $operand
   * @return $this
   */
  public function setLeftOperand(ExpressionNode $operand) {
    /** @var Node $operand */
    $this->left->replaceWith($operand);
    return $this;
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
  public function getRightOperand() {
    return $this->right;
  }

  /**
   * @param ExpressionNode $operand
   * @return $this
   */
  public function setRightOperand(ExpressionNode $operand) {
    /** @var Node $operand */
    $this->right->replaceWith($operand);
    return $this;
  }
}
