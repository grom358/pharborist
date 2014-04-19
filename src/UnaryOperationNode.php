<?php
namespace Pharborist;

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
}
