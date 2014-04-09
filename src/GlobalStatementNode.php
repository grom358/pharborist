<?php
namespace Pharborist;

/**
 * A global statement.
 *
 * For example, global $a, $b;
 */
class GlobalStatementNode extends StatementNode {
  protected $properties = array(
    'variables' => array(),
  );

  /**
   * @return (VariableNode|VariableVariableNode|CompoundVariableNode)[]
   */
  public function getVariables() {
    return $this->properties['variables'];
  }
}
