<?php
namespace Pharborist;

/**
 * An object property.
 *
 * For example, $object->property
 */
class ObjectPropertyNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $object;

  /**
   * @var Node
   */
  public $property;
}
