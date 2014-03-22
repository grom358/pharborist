<?php
namespace Pharborist;

/**
 * A break statement
 * @package Pharborist
 */
class ContinueStatementNode extends StatementNode {
  /**
   * An optional numeric argument which tells continue how many
   * enclosing structures are to be skipped to the end of.
   * @var IntegerNode
   */
  public $level = 1;
}
