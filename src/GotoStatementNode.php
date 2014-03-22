<?php
namespace Pharborist;

/**
 * A goto statement.
 * @package Pharborist
 */
class GotoStatementNode extends StatementNode {
  /**
   * @var Node
   */
  public $label;
}
