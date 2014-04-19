<?php
namespace Pharborist;

/**
 * A lookup to a class method.
 *
 * For example, MyClass::classMethod
 */
class ClassMethodCallNode extends CallNode implements VariableExpressionNode {
  protected $properties = array(
    'className' => NULL,
    'methodName' => NULL,
    'arguments' => NULL,
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
   * @return ExpressionNode[]
   */
  public function getArguments() {
    /** @var ArgumentListNode $arguments */
    $arguments = $this->properties['arguments'];
    return $arguments->getArguments();
  }
}
