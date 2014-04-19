<?php
namespace Pharborist;

/**
 * A reference variable.
 *
 * For example, &$a
 */
class ReferenceVariableNode extends ParentNode implements ExpressionNode {
  /**
   * @var VariableNode
   */
  protected $variable;

  /**
   * @return VariableNode
   */
  public function getVariable() {
    return $this->variable;
  }
}
