<?php
namespace Pharborist;

/**
 * A global statement. Eg. global $a, $b;
 * @package Pharborist
 */
class GlobalStatementNode extends StatementNode {
  /**
   * @var (VariableNode|VariableVariableNode|CompoundVariableNode)[]
   */
  public $variables = array();
}
