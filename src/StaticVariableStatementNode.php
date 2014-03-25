<?php
namespace Pharborist;

/**
 * A static variable statement.
 *
 * For example, static $a, $b = A_SCALAR;
 */
class StaticVariableStatementNode extends StatementNode {
  /**
   * @var StaticVariableNode[]
   */
  public $variables = array();
}
