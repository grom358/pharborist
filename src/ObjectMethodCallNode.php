<?php
namespace Pharborist;

/**
 * An object method call.
 *
 * For example, $object->method()
 */
class ObjectMethodCallNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $object;

  /**
   * @var Node
   */
  public $methodName;

  /**
   * @var ExpressionNode[]
   */
  public $arguments = array();
}
