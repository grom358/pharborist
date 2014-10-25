<?php
namespace Pharborist\Types;

use Pharborist\ExpressionNode;
use Pharborist\TokenNode;

/**
 * A string, like e.g. `'hello world'` or `"We are the knights who say, 'Ni!'"`. This
 * does *not* include strings interspersed with variables or other expressions --
 * those are handled by InterpolatedStringNode.
 *
 * @see \Pharborist\InterpolatedStringNode
 */
class StringNode extends TokenNode implements ExpressionNode, ScalarNode {
  /**
   * Creates a new constant string.
   *
   * @param string $text
   *  The text of the string.
   *
   * @return StringNode
   */
  public static function create($text) {
    return new StringNode(T_CONSTANT_ENCAPSED_STRING, $text);
  }

  /**
   * Returns the original value of the string (unenclosed by quotes).
   *
   * @return string
   */
  public function toValue() {
    $text = $this->getText();
    $quote_char = $text[0];
    $text = substr($text, 1, -1);
    if ($quote_char === '"') {
      $rules = array(
        preg_quote('\\\\') => '\\',
        preg_quote('\n') => "\n",
        preg_quote('\t') => "\t",
        preg_quote('\"') => '"',
        preg_quote('\$') => '$',
        preg_quote('\r') => "\r",
        preg_quote('\v') => "\v",
        preg_quote('\f') => "\f",
        preg_quote('\e') => "\e",
        '\\\\[0-7]{1,3}' => '__octal__',
        '\\\\x[0-9A-Fa-f]{1,2}' => '__hex__',
      );
    }
    else {
      $rules = array(
        preg_quote('\\\\') => '\\',
        preg_quote("\\'") => "'"
      );
    }
    $replacements = array_values($rules);
    $regex = '@(' . implode(')|(', array_keys($rules)) . ')@';
    return preg_replace_callback($regex, function ($matches) use ($replacements) {
      // find the first non-empty element (but skipping $matches[0]) using a quick for loop
      for ($i = 1; '' === $matches[$i]; ++$i);

      $match = $matches[0];
      $replacement = $replacements[$i - 1];
      if ($replacement === '__octal__') {
        $replacement = chr(octdec(substr($match, 1)));
      }
      elseif ($replacement === '__hex__') {
        $replacement = chr(hexdec(substr($match, 2)));
      }
      return $replacement;
    }, $text);
  }
}
