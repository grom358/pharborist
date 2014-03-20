<?php
namespace Pharborist;

/**
 * An array lookup. Eg. $array[0]
 * @package Pharborist
 */
class ArrayLookupNode extends Node {
  /**
   * @var Node
   */
  public $array;

  /**
   * @var Node
   */
  public $key;
}
