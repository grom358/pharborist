<?php
namespace Pharborist;

/**
 * A global statement, e.g. `global $a, $b`
 */
class GlobalStatementNode extends StatementNode {
  /**
   * @var CommaListNode
   */
  protected $variables;

  /**
   * @return CommaListNode
   */
  public function getVariableList() {
    return $this->variables;
  }

  /**
   * @return VariableExpressionNode[]
   */
  public function getVariables() {
    return $this->variables->getItems();
  }
}
