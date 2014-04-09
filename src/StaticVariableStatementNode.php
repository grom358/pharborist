<?php
namespace Pharborist;

/**
 * A static variable statement.
 *
 * For example, static $a, $b = A_SCALAR;
 */
class StaticVariableStatementNode extends StatementNode {
  protected $properties = array(
    'variables' => array(),
  );

  /**
   * @return StaticVariableNode[]
   */
  public function getVariables() {
    return $this->properties['variables'];
  }
}
