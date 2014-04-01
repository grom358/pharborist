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
  public $className;

  /**
   * @var Node
   */
  public $constantName;
}
