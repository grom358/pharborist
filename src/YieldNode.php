<?php
namespace Pharborist;

/**
 * A yield expression.
 * @package Pharborist
 */
class YieldNode extends Node {
  /**
   * @var Node
   */
  public $key;

  /**
   * @var Node
   */
  public $value;
}
