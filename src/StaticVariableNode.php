<?php
namespace Pharborist;

/**
 * A static variable declaration.
 *
 * For example, $a = A_SCALAR_VALUE
 */
class StaticVariableNode extends ParentNode {
  /**
   * @var VariableNode
   */
  protected $name;

  /**
   * @var ExpressionNode
   */
  protected $initialValue;

  /**
   * @return VariableNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return Node
   */
  public function getInitialValue() {
    return $this->initialValue;
  }
}
