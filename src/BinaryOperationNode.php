<?php
namespace Pharborist;

/**
 * A binary operation.
 */
abstract class BinaryOperationNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'left' => NULL,
    'operator' => NULL,
    'right' => NULL,
  );

  /**
   * @return ExpressionNode
   */
  public function getLeft() {
    return $this->properties['left'];
  }

  /**
   * @return Node
   */
  public function getOperator() {
    return $this->properties['operator'];
  }

  /**
   * @return ExpressionNode
   */
  public function getRight() {
    return $this->properties['right'];
  }
}
