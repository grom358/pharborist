<?php
namespace Pharborist;

/**
 * An integer scalar.
 */
class IntegerNode extends TokenNode implements ExpressionNode {
  /**
   * @return int
   */
  public function getValue() {
    return (int) $this->getText();
  }
}
