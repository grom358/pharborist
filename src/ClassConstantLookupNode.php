<?php
namespace Pharborist;

/**
 * A class constant lookup.
 *
 * For example: MyClass::MY_CONST
 */
class ClassConstantLookupNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'className' => NULL,
    'constantName' => NULL,
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
  public function getConstantName() {
    return $this->properties['constantName'];
  }
}
