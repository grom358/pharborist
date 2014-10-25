<?php
namespace Pharborist\Variables;

use Pharborist\Functions\LexicalVariableNode;
use Pharborist\TokenNode;

/**
 * A basic variable.
 *
 * For example, $a
 */
class VariableNode extends TokenNode implements VariableExpressionNode, LexicalVariableNode {
  /**
   * @return string
   *  The variable name, without the leading $.
   */
  public function getName() {
    return ltrim($this->getText(), '$');
  }

  /**
   * @param string $name
   *  The name of the parameter, with or without the leading $.
   *
   * @return $this
   */
  public function setName($name) {
    return $this->setText('$' . ltrim($name, '$'));
  }
}
