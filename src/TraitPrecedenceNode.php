<?php
namespace Pharborist;

/**
 * A trait precedence declaration.
 *
 * For example, A::bigTalk insteadof B;
 */
class TraitPrecedenceNode extends ParentNode {
  protected $properties = array(
    'traitMethodReference' => NULL,
    'traitNames' => array(),
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
    return $this->properties['traitNames'];
  }
}
