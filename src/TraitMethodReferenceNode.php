<?php
namespace Pharborist;

/**
 * A reference to trait method name as part of a trait use declaration.
 */
class TraitMethodReferenceNode extends ParentNode {
  protected $properties = array(
    'traitName' => NULL,
    'methodReference' => NULL,
  );

  /**
   * @return NamespacePathNode
   */
  public function getTraitName() {
    return $this->properties['traitName'];
  }

  /**
   * @return Node
   */
  public function getMethodReference() {
    return $this->properties['methodReference'];
  }
}
