<?php
namespace Pharborist;

/**
 * An object method call.
 *
 * For example, $object->method()
 */
class ObjectMethodCallNode extends CallNode implements VariableExpressionNode {
  /**
   * @var Node
   */
  protected $object;

  /**
   * @var Node
   */
  protected $methodName;

  /**
   * @var ArgumentListNode
   */
  protected $arguments;

  /**
   * @return Node
   */
  public function getObject() {
    return $this->object;
  }

  /**
   * @return Node
   */
  public function getMethodName() {
    return $this->methodName;
  }

  public function getArguments() {
    return $this->arguments->getArguments();
  }
}
