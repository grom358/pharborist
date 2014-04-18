<?php
namespace Pharborist;

/**
 * A class member lookup.
 *
 * For example, MyClass::$a
 */
class ClassMemberLookupNode extends ParentNode implements VariableExpressionNode {
  protected $properties = array(
    'className' => NULL,
    'memberName' => NULL,
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
  public function getMemberName() {
    return $this->properties['memberName'];
  }
}
