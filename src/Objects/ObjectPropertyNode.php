<?php
namespace Pharborist\Objects;

use Pharborist\Node;
use Pharborist\ParentNode;
use Pharborist\TokenNode;
use Pharborist\Variables\VariableExpressionNode;

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
   * Returns the name of the property if it's an identifier (ie. T_STRING TokenNode).
   *
   * @return string|NULL
   *   Name of the property or NULL if not an identifier (eg. dynamic property
   *   name).
   */
  public function getPropertyName() {
    $root_property = $this->getRootProperty();
    if ($root_property instanceof TokenNode && $root_property->getType() === T_STRING) {
      return $root_property->getText();
    }
    return NULL;
  }

  /**
   * Returns the root property.
   *
   * For example, given an expression like $foo->bar->baz this method will
   * return the identifier (T_STRING TokenNode) 'bar'.
   *
   * @return Node
   *   The node for the root property.
   */
  public function getRootProperty() {
    if ($this->object instanceof ObjectPropertyNode) {
      return $this->object->getRootProperty();
    }
    else {
      return $this->property;
    }
  }
}
