<?php
namespace Pharborist;

/**
 * A class member list declaration.
 */
class ClassMemberListNode extends ParentNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var ModifiersNode
   */
  public $modifiers;

  /**
   * @var ClassMemberNode[]
   */
  public $members = array();
}
