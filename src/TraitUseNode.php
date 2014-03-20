<?php
namespace Pharborist;

/**
 * A trait use declaration.
 * @package Pharborist
 */
class TraitUseNode extends Node {
  /**
   * @var NamespacePathNode[]
   */
  public $traits;

  /**
   * @var (TraitPrecedenceNode|TraitAliasNode)[]
   */
  public $adaptations;
}
