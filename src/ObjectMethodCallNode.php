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

  /**
   * @param string|Node $method_name
   * @return $this
   */
  public function setMethodName($method_name) {
    if (is_string($method_name)) {
      $method_name = Token::identifier($method_name);
    }
    $this->methodName->replaceWith($method_name);
    $this->methodName = $method_name;
    return $this;
  }
}
