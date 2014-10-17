<?php
namespace Pharborist;

/**
 * An object property access, e.g. `$object->property`.
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

  /**
   * Returns the name of the property if it's a T_STRING.
   *
   * @return string|NULL
   */
  public function getPropertyName() {
    $root_property = $this->getRootProperty();
    if ($root_property instanceof TokenNode && $root_property->getType() === T_STRING) {
      return $root_property->getText();
    }
  }

  /**
   * Returns the root of the property.
   *
   * For example, given an expression like $node->body['und'][0]['value'],
   * this method will return a T_STRING TokenNode for 'body'.
   *
   * @return Node
   */
  public function getRootProperty() {
    if ($this->property instanceof ObjectPropertyNode) {
      return $this->property->getRootProperty();
    }
    elseif ($this->property instanceof ArrayLookupNode) {
      return $this->property->getRoot();
    }
    else {
      return $this->property;
    }
  }
}
