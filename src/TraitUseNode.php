<?php
namespace Pharborist;

/**
 * A trait use declaration.
 */
class TraitUseNode extends ParentNode {
  /**
   * @var NamespacePathNode[]
   */
  public $traits = array();

  /**
   * @var (TraitPrecedenceNode|TraitAliasNode)[]
   */
  public $adaptations = array();
}
