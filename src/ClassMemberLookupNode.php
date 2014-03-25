<?php
namespace Pharborist;

/**
 * A class member lookup.
 *
 * For example, MyClass::$a
 */
class ClassMemberLookupNode extends ParentNode {
  /**
   * @var Node
   */
  public $className;

  /**
   * @var Node
   */
  public $memberName;
}
