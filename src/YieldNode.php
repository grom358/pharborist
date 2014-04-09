<?php
namespace Pharborist;

/**
 * A yield expression.
 */
class YieldNode extends ParentNode implements ExpressionNode {
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
