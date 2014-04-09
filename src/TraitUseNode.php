<?php
namespace Pharborist;

/**
 * A trait use declaration.
 */
class TraitUseNode extends ParentNode {
  protected $properties = array(
    'traits' => array(),
    'adaptations' => array(),
  );

  /**
   * @return NamespacePathNode[]
   */
  public function getTraits() {
    return $this->properties['traits'];
  }

  /**
   * @return (TraitPrecedenceNode|TraitAliasNode)[]
   */
  public function getAdaptations() {
    return $this->properties['adaptations'];
  }
}
