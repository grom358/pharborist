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
  public $traits = array();

  /**
   * @var (TraitPrecedenceNode|TraitAliasNode)[]
   */
  public $adaptations = array();
}
