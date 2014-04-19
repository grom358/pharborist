<?php
namespace Pharborist;

/**
 * A class name scalar.
 *
 * For example, MyClass::class
 */
class ClassNameScalarNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  protected $className;

  /**
   * @return Node
   */
  public function getClassName() {
    return $this->className;
  }
}
