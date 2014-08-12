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
   * @return $this
   */
  public function setOperator(TokenNode $operator) {
    $this->operator = $operator;
    return $this;
  }

  /**
   * @return ExpressionNode
   */
  public function getOperand() {
    return $this->operand;
  }
  
  /**
   * @return $this
   */
  public function setOperand(ExpressionNode $operand) {
    $this->operand = $operand;
    return $this;
  }
  
  /**
   * @return $this
   */
  public function __toString() {
    return $this->getOperator() . ' ' . $this->getOperand();
  }
}
