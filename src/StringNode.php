<?php
namespace Pharborist;

/**
 * A string constant.
 *
 * For example, 'hello world'
 */
class StringNode extends TokenNode implements ExpressionNode {

  public function setText($value) {
    // If $value is already quoted, no need to do anything.
    if (!preg_match('#^(\'|").+\1$#', $value)) {
      // If $value contains an unescaped variable, use double quotes.
      if (preg_match('/[^\\\\]\$/', $value)) {
        $value = '"' . $value . '"';
      }
      else {
        $value = "'$value'";
      }
    }
    return parent::setText($value);
  }

}
