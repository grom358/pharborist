<?php
namespace Pharborist\Types;

use Pharborist\ExpressionNode;
use Pharborist\TokenNode;

/**
 * A float scalar, like 29.8 or 3.141.
 */
class FloatNode extends TokenNode implements ExpressionNode, ScalarNode {
  /**
   * @return float
   */
  public function toValue() {
    return (float) $this->getText();
  }
}
