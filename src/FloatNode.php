<?php
namespace Pharborist;

/**
 * A float scalar.
 */
class FloatNode extends TokenNode implements ExpressionNode {
  /**
   * @return float
   */
  public function getValue() {
    return (float) $this->getText();
  }
}
