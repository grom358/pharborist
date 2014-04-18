<?php
namespace Pharborist;

/**
 * An array lookup.
 *
 * For example $array[0]
 */
class ArrayLookupNode extends ParentNode implements VariableExpressionNode {
  protected $properties = array(
    'array' => NULL,
    'key' => NULL,
  );

  /**
   * @return Node
   */
  public function getArray() {
    return $this->properties['array'];
  }

  /**
   * @return Node
   */
  public function getKey() {
    return $this->properties['key'];
  }
}
