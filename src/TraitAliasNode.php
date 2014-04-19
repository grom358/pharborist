<?php
namespace Pharborist;

/**
 * A trait alias.
 *
 * For example, B::bigTalk as talk;
 */
class TraitAliasNode extends TraitAdaptationStatementNode {
  /**
   * @var NamespacePathNode|TraitMethodReferenceNode
   */
  protected $traitMethodReference;

  /**
   * @var TokenNode
   */
  protected $visibility;

  /**
   * @var TokenNode
   */
  protected $alias;

  /**
   * @return NamespacePathNode|TraitMethodReferenceNode
   */
  public function getTraitMethodReference() {
    return $this->traitMethodReference;
  }

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
  }

  /**
   * @return TokenNode
   */
  public function getAlias() {
    return $this->alias;
  }
}
