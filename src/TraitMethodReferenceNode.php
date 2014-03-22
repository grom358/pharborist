<?php
namespace Pharborist;

/**
 * A reference to trait method name instead a trait use declaration.
 * @package Pharborist
 */
class TraitMethodReferenceNode extends Node {
  /**
   * @var NamespacePathNode
   */
  public $traitName;

  /**
   * @var Node
   */
  public $methodName;
}
