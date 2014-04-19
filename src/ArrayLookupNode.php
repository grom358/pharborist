<?php
namespace Pharborist;

/**
 * An array lookup.
 *
 * For example $array[0]
 */
class ArrayLookupNode extends ParentNode implements VariableExpressionNode {
  /**
   * @var Node
   */
  protected $array;

  /**
   * @var Node
   */
  protected $key;

  /**
   * @return Node
   */
  public function getArray() {
    return $this->array;
  }

  /**
   * @return Node
   */
  public function getKey() {
    return $this->key;
  }
}
