<?php
namespace Pharborist;

/**
 * A yield expression.
 */
class YieldNode extends ParentNode implements ExpressionNode {
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
