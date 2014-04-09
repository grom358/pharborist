<?php
namespace Pharborist;

/**
 * A variable variable.
 *
 * For example, $$a
 */
class VariableVariableNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'variable' => NULL,
  );

  /**
   * @return Node
   */
  public function getVariable() {
    return $this->properties['variable'];
  }
}
