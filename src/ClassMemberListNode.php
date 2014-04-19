<?php
namespace Pharborist;

/**
 * A class member list declaration.
 */
class ClassMemberListNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var ModifiersNode
   */
  protected $modifiers;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return ModifiersNode
   */
  public function getModifiers() {
    return $this->modifiers;
  }

  /**
   * @return ClassMemberNode[]
   */
  public function getMembers() {
    return $this->childrenByInstance('\Pharborist\ClassMemberNode');
  }
}
