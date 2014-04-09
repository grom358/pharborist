<?php
namespace Pharborist;

/**
 * A function parameter.
 */
class ParameterNode extends ParentNode {
  protected $properties = array(
    'classType' => NULL,
    'reference' => NULL,
    'name' => NULL,
    'defaultValue' => NULL,
  );

  /**
   * @return Node
   */
  public function getClassType() {
    return $this->properties['classType'];
  }

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->properties['reference'];
  }

  /**
   * @return Node
   */
  public function getName() {
    return $this->properties['name'];
  }

  /**
   * @return Node
   */
  public function getDefaultValue() {
    return $this->properties['defaultValue'];
  }
}
