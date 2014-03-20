<?php
namespace Pharborist;

/**
 * Class declaration.
 * @package Pharborist
 */
class ClassNode extends Node {
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
