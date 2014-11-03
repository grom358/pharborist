<?php
namespace Pharborist\Operators;

use Pharborist\ExpressionNode;
use Pharborist\ParentNode;
use Pharborist\TokenNode;


/**
 * An unary operation.
 */
abstract class UnaryOperationNode extends ParentNode implements ExpressionNode {
  /**
   * @var TokenNode
   */
  protected $operator;

  /**
   * @var ExpressionNode
   */
  protected $operand;

  /**
   * @return TokenNode
   */
  public function getOperator() {
    return $this->operator;
  }

  /**
   * @return ExpressionNode
   */
  public function getOperand() {
    return $this->operand;
  }

  /**
   * @param ExpressionNode $operand
   * @return $this
   */
  public function setOperand(ExpressionNode $operand) {
    /** @var \Pharborist\Node $operand */
    $this->operand->replaceWith($operand);
    return $this;
  }
}
