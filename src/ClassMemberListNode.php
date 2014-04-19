<?php
namespace Pharborist;

/**
 * A class member list declaration.
 */
class ClassMemberListNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
    'modifiers' => NULL,
    'members' => NULL,
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
    return $this->childrenByInstance('\Pharborist\ClassMemberNode');
  }
}
