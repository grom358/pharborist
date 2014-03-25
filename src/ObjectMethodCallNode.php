<?php
namespace Pharborist;

/**
 * An object method call.
 *
 * For example, $object->method()
 */
class ObjectMethodCallNode extends ParentNode {
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
