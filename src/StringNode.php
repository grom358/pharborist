<?php
namespace Pharborist;

/**
 * A string constant.
 *
 * For example, 'hello world'
 */
class StringNode extends TokenNode implements ExpressionNode {
  /**
   * Creates a new constant string.
   *
   * @param string $text
   *  The text of the string.
   *
   * @return \Pharborist\StringNode
   */
  public static function create($text) {
    return new StringNode(T_CONSTANT_ENCAPSED_STRING, $text);
  }

  /**
   * Returns the original value of the string (unenclosed by quotes).
   *
   * @return string
   */
  public function getValue() {
    return trim($this, '\'"');
  }
}
