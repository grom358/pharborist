<?php
namespace Pharborist;

/**
 * An integer scalar.
 */
class IntegerNode extends TokenNode implements ExpressionNode {
  /**
   * @return integer
   */
  public function toInteger() {
    return (int) $this->getText();
  }
}
