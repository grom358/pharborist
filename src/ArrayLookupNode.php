<?php
namespace Pharborist;

/**
 * An array lookup.
 *
 * For example $array[0]
 */
class ArrayLookupNode extends ParentNode {
  /**
   * @var Node
   */
  public $array;

  /**
   * @var Node
   */
  public $key;
}
