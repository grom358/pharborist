<?php
namespace Pharborist;

/**
 * A class member lookup.
 *
 * For example, MyClass::$a
 */
class ClassMemberLookupNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $className;

  /**
   * @var Node
   */
  public $memberName;
}
