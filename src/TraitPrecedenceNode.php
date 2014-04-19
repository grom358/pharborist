<?php
namespace Pharborist;

/**
 * A trait precedence declaration.
 *
 * For example, A::bigTalk insteadof B;
 */
class TraitPrecedenceNode extends StatementNode {
  protected $properties = array(
    'traitMethodReference' => NULL,
    'traitNames' => NULL,
  );

  /**
   * @return TraitMethodReferenceNode
   */
  public function getTraitMethodReference() {
    return $this->properties['traitMethodReference'];
  }

  /**
   * @return NamespacePathNode[]
   */
  public function getTraitNames() {
    /** @var CommaListNode $trait_names */
    $trait_names = $this->properties['traitNames'];
    return $trait_names->getItems();
  }
}
