<?php
namespace Pharborist;

/**
 * A trait alias.
 *
 * For example, B::bigTalk as talk;
 */
class TraitAliasNode extends StatementNode {
  protected $properties = array(
    'traitMethodReference' => NULL,
    'visibility' => NULL,
    'alias' => NULL,
  );

  /**
   * @return NamespacePathNode|TraitMethodReferenceNode
   */
  public function getTraitMethodReference() {
    return $this->properties['traitMethodReference'];
  }

  /**
   * @return Node
   */
  public function getVisibility() {
    return $this->properties['visibility'];
  }

  /**
   * @return Node
   */
  public function getAlias() {
    return $this->properties['alias'];
  }
}
