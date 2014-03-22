<?php
namespace Pharborist;

/**
 * A return statement.
 * @package Pharborist
 */
class ReturnStatementNode extends StatementNode {
  /**
   * An optional value to return.
   * @var Node
   */
  public $value;
}
