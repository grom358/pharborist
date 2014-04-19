<?php
namespace Pharborist;

/**
 * An object method call.
 *
 * For example, $object->method()
 */
class ObjectMethodCallNode extends CallNode implements VariableExpressionNode {
  protected $properties = array(
    'object' => NULL,
    'methodName' => NULL,
    'arguments' => NULL,
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
    /** @var ArgumentListNode $arguments */
    $arguments = $this->properties['arguments'];
    return $arguments->getArguments();
  }
}
