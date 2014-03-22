<?php
namespace Pharborist;

/**
 * An object property. Eg. $object->property
 * @package Pharborist
 */
class ObjectPropertyNode extends Node {
  /**
   * @var Node
   */
  public $object;

  /**
   * @var Node
   */
  public $property;
}
