<?php
namespace Pharborist;

/**
 * A global statement.
 *
 * For example, global $a, $b;
 */
class GlobalStatementNode extends StatementNode {
  /**
   * @var (VariableNode|VariableVariableNode|CompoundVariableNode)[]
   */
  public $variables = array();
}
