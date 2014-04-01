<?php
namespace Pharborist;

/**
 * A lookup to a class method.
 *
 * For example, MyClass::classMethod
 */
class ClassMethodCallNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $className;

  /**
   * @var Node
   */
  public $methodName;

  /**
   * @var Node[]
   */
  public $arguments = array();
}
