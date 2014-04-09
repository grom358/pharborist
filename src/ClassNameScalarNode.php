<?php
namespace Pharborist;

/**
 * A class name scalar.
 *
 * For example, MyClass::class
 */
class ClassNameScalarNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'className' => NULL,
  );

  /**
   * @return Node
   */
  public function getClassName() {
    return $this->properties['className'];
  }
}
