<?php
namespace Pharborist;

/**
 * A class member list declaration.
 */
class ClassMemberListNode extends ParentNode {
  protected $properties = array(
    'modifiers' => NULL,
    'members' => array(),
  );

  /**
   * @return ModifiersNode
   */
  public function getModifiers() {
    return $this->properties['modifiers'];
  }

  /**
   * @return ClassMemberNode[]
   */
  public function getMembers() {
    return $this->properties['members'];
  }
}
