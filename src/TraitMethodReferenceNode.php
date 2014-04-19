<?php
namespace Pharborist;

/**
 * A reference to trait method name as part of a trait use declaration.
 */
class TraitMethodReferenceNode extends ParentNode {
  /**
   * @var NamespacePathNode
   */
  protected $traitName;

  /**
   * @var Node
   */
  protected $methodReference;

  /**
   * @return NamespacePathNode
   */
  public function getTraitName() {
    return $this->traitName;
  }

  /**
   * @return Node
   */
  public function getMethodReference() {
    return $this->methodReference;
  }
}
