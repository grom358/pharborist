<?php
namespace Pharborist;

/**
 * A lookup to a class method.
 *
 * For example, MyClass::classMethod
 */
class ClassMethodCallNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'className' => NULL,
    'methodName' => NULL,
    'arguments' => array(),
  );

  /**
   * @return Node
   */
  public function getClassName() {
    return $this->properties['className'];
  }

  /**
   * @return Node
   */
  public function getMethodName() {
    return $this->properties['methodName'];
  }

  /**
   * @return Node[]
   */
  public function getArguments() {
    return $this->properties['arguments'];
  }
}
