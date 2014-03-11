<?php
namespace Pharborist;

/**
 * A trait declaration.
 * @package Pharborist
 */
class TraitNode extends Node {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $extends;

  /**
   * @var Node[]
   */
  public $implements = array();

  /**
   * @var Node[]
   */
  public $statements = array();
}
