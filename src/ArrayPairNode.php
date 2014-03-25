<?php
namespace Pharborist;

/**
 * A key/value pair element in php array.
 */
class ArrayPairNode extends ParentNode {
  /**
   * @var Node
   */
  public $key;

  /**
   * @var Node
   */
  public $value;
}
