<?php
namespace Pharborist\ControlStructures;

use Pharborist\TokenNode;
use Pharborist\StatementNode;

/**
 * A goto statement.
 */
class GotoStatementNode extends StatementNode {
  /**
   * @var TokenNode
   */
  protected $label;

  /**
   * @return TokenNode
   */
  public function getLabel() {
    return $this->label;
  }
}
