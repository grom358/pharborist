<?php
namespace Pharborist;

/**
 * foreach control structure.
 * @package Pharborist
 */
class ForeachNode extends Node {
  /**
   * @var Node
   */
  public $onEach;

  /**
   * @var Node
   */
  public $key;

  /**
   * @var Node
   */
  public $value;

  /**
   * @var Node
   */
  public $body;
}
