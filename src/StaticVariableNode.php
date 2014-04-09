<?php
namespace Pharborist;

/**
 * A static variable declaration.
 *
 * For example, $a = A_SCALAR_VALUE
 */
class StaticVariableNode extends ParentNode {
  protected $properties = array(
    'name' => NULL,
    'initialValue' => NULL,
  );

  /**
   * @return Node
   */
  public function getName() {
    return $this->properties['name'];
  }

  /**
   * @return Node
   */
  public function getInitialValue() {
    return $this->properties['initialValue'];
  }
}
