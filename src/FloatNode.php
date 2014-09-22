<?php
namespace Pharborist;

/**
 * A float scalar.
 */
class FloatNode extends TokenNode implements ExpressionNode, ScalarNode {
  /**
   * @return float
   */
  public function toValue() {
    return (float) $this->getText();
  }
}
