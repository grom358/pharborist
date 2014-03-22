<?php
namespace Pharborist;

/**
 * A trait precedence declaration. Eg. A::bigTalk insteadof B;
 * @package Pharborist
 */
class TraitPrecedenceNode extends Node {
  /**
   * @var Node
   */
  public $methodReference;

  /**
   * @var NamespacePathNode[]
   */
  public $traitNames = array();
}
