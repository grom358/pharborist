<?php
namespace Pharborist;

/**
 * A try control structure.
 * @package Pharborist
 */
class TryNode extends Node {
  /**
   * @var Node
   */
  public $try;

  /**
   * @var Node[]
   */
  public $catches;

  /**
   * @var Node
   */
  public $finally;
}
