<?php
namespace Pharborist;

/**
 * A reference variable.
 *
 * For example, &$a
 */
class ReferenceVariableNode extends ParentNode implements ExpressionNode {
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
