<?php
namespace Pharborist;

/**
 * An object method call.
 *
 * For example, $object->method()
 */
class ObjectMethodCallNode extends ParentNode implements ExpressionNode {
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

  /**
   * @return ExpressionNode[]
   */
  public function getArguments() {
    return $this->properties['arguments'];
  }
}
