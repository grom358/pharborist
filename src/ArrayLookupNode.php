<?php
namespace Pharborist;

/**
 * An array lookup.
 *
 * For example $array[0]
 */
class ArrayLookupNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $array;

  /**
   * @var Node
   */
  public $key;
}
