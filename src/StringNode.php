<?php
namespace Pharborist;

/**
 * A string constant.
 *
 * For example, 'hello world'
 */
class StringNode extends TokenNode implements ExpressionNode {
  /**
   * Returns the original value of the string (unenclosed by quotes).
   *
   * @return string
   */
  public function getValue() {
    return trim($this, '\'"');
  }
}
