<?php
namespace Pharborist;

/**
 * A global statement, e.g. `global $a, $b`
 */
class GlobalStatementNode extends StatementNode {
  /**
   * @return VariableExpressionNode[]
   */
  public function getVariables() {
    return $this->childrenByInstance('\Pharborist\VariableExpressionNode');
  }
}
