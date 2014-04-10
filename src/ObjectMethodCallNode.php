<?php
namespace Pharborist;

/**
 * An object method call.
 *
 * For example, $object->method()
 */
class ObjectMethodCallNode extends CallNode implements ExpressionNode {
  protected $properties = array(
    'object' => NULL,
    'methodName' => NULL,
    'arguments' => array(),
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
  public function getMethodName() {
    return $this->properties['methodName'];
  }

  public function getArguments() {
    return $this->properties['arguments'];
  }
}
