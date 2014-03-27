<?php
namespace Pharborist;

/**
 * A trait precedence declaration.
 *
 * For example, A::bigTalk insteadof B;
 */
class TraitPrecedenceNode extends ParentNode {
  /**
   * @var TraitMethodReferenceNode
   */
  public $traitMethodReference;

  /**
   * @var NamespacePathNode[]
   */
  public $traitNames = array();
}
