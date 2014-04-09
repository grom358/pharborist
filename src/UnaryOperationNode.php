<?php
namespace Pharborist;

/**
 * An unary operation.
 */
abstract class UnaryOperationNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'operator' => NULL,
    'operand' => NULL,
  );

  /**
   * @return TokenNode
   */
  public function getOperator() {
    return $this->properties['operator'];
  }

  /**
   * @return ExpressionNode
   */
  public function getOperand() {
    return $this->properties['operand'];
  }
}
