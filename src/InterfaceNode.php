<?php
namespace Pharborist;

/**
 * An interface declaration.
 * @package Pharborist
 */
class InterfaceNode extends Node {
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
