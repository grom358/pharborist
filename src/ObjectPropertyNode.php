<?php
namespace Pharborist;

/**
 * An object property.
 *
 * For example, $object->property
 */
class ObjectPropertyNode extends ParentNode implements VariableExpressionNode {
  /**
   * @var Node
   */
  protected $object;

  /**
   * @var Node
   */
  protected $property;

  /**
   * @return Node
   */
  public function getObject() {
    return $this->object;
  }

  /**
   * @return Node
   */
  public function getProperty() {
    return $this->property;
  }
}
