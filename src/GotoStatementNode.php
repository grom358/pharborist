<?php
namespace Pharborist;

/**
 * A goto statement.
 */
class GotoStatementNode extends StatementNode {
  /**
   * @var Node
   */
  public $label;
}
