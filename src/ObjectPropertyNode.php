<?php
namespace Pharborist;

/**
 * An object property.
 *
 * For example, $object->property
 */
class ObjectPropertyNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'object' => NULL,
    'property' => NULL,
  );

  /**
   * @return Node
   */
  public function getObject() {
    return $this->properties['object'];
  }

  /**
   * @return Node
   */
  public function getProperty() {
    return $this->properties['property'];
  }
}
