<?php
namespace Pharborist;

/**
 * A trait precedence declaration.
 *
 * For example, A::bigTalk insteadof B;
 */
class TraitPrecedenceNode extends StatementNode {
  /**
   * @var TraitMethodReferenceNode
   */
  protected $traitMethodReference;

  /**
   * @var CommaListNode
   */
  protected $traitNames;

  /**
   * @return TraitMethodReferenceNode
   */
  public function getTraitMethodReference() {
    return $this->traitMethodReference;
  }

  /**
   * @return NamespacePathNode[]
   */
  public function getTraitNames() {
    return $this->traitNames->getItems();
  }
}
