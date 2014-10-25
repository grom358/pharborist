<?php
namespace Pharborist\Variables;

use Pharborist\ParentNode;
use Pharborist\Node;

/**
 * A variable variable.
 *
 * For example, $$a
 */
class VariableVariableNode extends ParentNode implements VariableExpressionNode {
  /**
   * @var Node
   */
  protected $variable;

  /**
   * @return Node
   */
  public function getVariable() {
    return $this->variable;
  }
}
