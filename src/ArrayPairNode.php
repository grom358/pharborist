<?php
namespace Pharborist;

/**
 * A key/value pair element in php array.
 * @package Pharborist
 */
class ArrayPairNode extends Node {
  /**
   * @var Node
   */
  public $key;

  /**
   * @var Node
   */
  public $value;
}
