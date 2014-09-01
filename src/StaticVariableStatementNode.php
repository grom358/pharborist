<?php
namespace Pharborist;

/**
 * A static variable statement.
 *
 * For example, static $a, $b = A_SCALAR;
 */
class StaticVariableStatementNode extends StatementNode {
  use DocCommentTrait;

  /**
   * @return StaticVariableNode[]
   */
  public function getVariables() {
    return $this->childrenByInstance('\Pharborist\StaticVariableNode');
  }
}
