<?php
namespace Pharborist;

/**
 * A class constant lookup.
 *
 * For example: MyClass::MY_CONST
 */
class ClassConstantLookupNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  protected $className;

  /**
   * @var Node
   */
  protected $constantName;

  /**
   * @return Node
   */
  public function getClassName() {
    return $this->className;
  }

  /**
   * @return Node
   */
  public function getConstantName() {
    return $this->constantName;
  }
}
