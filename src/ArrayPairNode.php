<?php
namespace Pharborist;

/**
 * A key/value pair element in php array.
 */
class ArrayPairNode extends ParentNode implements ArrayElementNode {
  /**
   * @var Node
   */
  protected $key;

  /**
   * @var Node
   */
  protected $value;

  /**
   * @return Node
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * @return Node
   */
  public function getValue() {
    return $this->value;
  }
}
