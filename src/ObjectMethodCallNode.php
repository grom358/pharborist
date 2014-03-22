<?php
namespace Pharborist;

/**
 * An object method call. Eg. $object->method()
 * @package Pharborist
 */
class ObjectMethodCallNode extends Node {
  /**
   * @var Node
   */
  public $object;

  /**
   * @var Node
   */
  public $methodName;

  /**
   * @var Node[]
   */
  public $arguments = array();
}
