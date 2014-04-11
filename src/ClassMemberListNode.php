<?php
namespace Pharborist;

/**
 * A class member list declaration.
 */
class ClassMemberListNode extends ParentNode {
  protected $properties = array(
    'docComment' => NULL,
    'modifiers' => NULL,
    'members' => array(),
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }

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
