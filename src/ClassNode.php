<?php
namespace Pharborist;

/**
 * Class declaration.
 */
class ClassNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var Node
   */
  public $abstract;

  /**
   * @var Node
   */
  public $final;

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
