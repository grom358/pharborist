<?php
namespace Pharborist;

/**
 * A reference to trait method name as part of a trait use declaration.
 */
class TraitMethodReferenceNode extends ParentNode {
  /**
   * @var NamespacePathNode
   */
  public $traitName;

  /**
   * @var Node
   */
  public $methodReference;
}
