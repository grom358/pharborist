<?php
namespace Pharborist;

/**
 * A trait use declaration.
 */
class TraitUseNode extends StatementNode {
  /**
   * @var CommaListNode
   */
  protected $traits;

  /**
   * @var StatementBlockNode
   */
  protected $adaptations;

  /**
   * @return NamespacePathNode[]
   */
  public function getTraits() {
    return $this->traits->getItems();
  }

  /**
   * @return (TraitPrecedenceNode|TraitAliasNode)[]
   */
  public function getAdaptations() {
    return $this->adaptations->getStatements();
  }
}
