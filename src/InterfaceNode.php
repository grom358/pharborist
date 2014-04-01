<?php
namespace Pharborist;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var NamespacePathNode[]
   */
  public $extends = array();

  /**
   * @var (InterfaceMethodNode|ConstantDeclarationNode)[]
   */
  public $statements = array();
}
