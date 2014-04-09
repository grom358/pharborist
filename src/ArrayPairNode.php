<?php
namespace Pharborist;

/**
 * A key/value pair element in php array.
 */
class ArrayPairNode extends ParentNode {
  protected $properties = array(
    'key' => NULL,
    'value' => NULL,
  );

  /**
   * @return Node
   */
  public function getKey() {
    return $this->properties['key'];
  }

  /**
   * @return Node
   */
  public function getValue() {
    return $this->properties['value'];
  }
}
