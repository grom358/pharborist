<?php
namespace Pharborist\Variables;

use Pharborist\CommaListNode;
use Pharborist\NodeCollection;
use Pharborist\StatementNode;

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
   * @return NodeCollection|VariableExpressionNode[]
   */
  public function getVariables() {
    return $this->variables->getItems();
  }
}
