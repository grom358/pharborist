<?php
namespace Pharborist;

/**
 * A float scalar.
 */
class FloatNode extends TokenNode implements ExpressionNode {
  /**
   * @return float
   */
  public function toFloat() {
    return (float) $this->getText();
  }
}
