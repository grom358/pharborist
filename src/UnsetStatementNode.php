<?php
namespace Pharborist;

/**
 * A unset statement.
 * @package Pharborist
 */
class UnsetStatementNode extends StatementNode {
  /**
   * @var UnsetNode
   */
  public $functionCall;
}
