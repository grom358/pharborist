<?php
namespace Pharborist;

/**
 * An object property.
 *
 * For example, $object->property
 */
class ObjectPropertyNode extends ParentNode {
  /**
   * @var Node
   */
  public $object;

  /**
   * @var Node
   */
  public $property;
}
