<?php
namespace Pharborist;

/**
 * An integer scalar.
 */
class IntegerNode extends TokenNode implements ExpressionNode, ScalarNode {
  /**
   * @return int
   */
  public function toValue() {
    return (int) $this->getText();
  }
}
