<?php
namespace Pharborist;

/**
 * A lookup to a class method.
 *
 * For example, MyClass::classMethod
 */
class ClassMethodCallNode extends CallNode implements VariableExpressionNode {
  /**
   * @var NameNode|Node
   */
  protected $className;

  /**
   * @var Node
   */
  protected $methodName;

  /**
   * @return NameNode|Node
   */
  public function getClassName() {
    return $this->className;
  }

  /**
   * @param string|Node $class_name
   * @return $this
   */
  public function setClassName($class_name) {
    if (is_string($class_name)) {
      $class_name = Token::string($class_name);
    }
    $this->className->replaceWith($class_name);
    $this->className = $class_name;
    return $this;
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
      $method_name = Token::string($method_name);
    }
    $this->methodName->replaceWith($method_name);
    $this->methodName = $method_name;
    return $this;
  }
}
