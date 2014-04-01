<?php
namespace Pharborist;

/**
 * A trait declaration.
 */
class TraitNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var NamespacePathNode
   */
  public $extends;

  /**
   * @var NamespacePathNode[]
   */
  public $implements = array();

  /**
   * @var (ClassMemberListNode|ClassMethodNode|ConstantDeclarationNode|TraitUseNode)[]
   */
  public $statements = array();
}
