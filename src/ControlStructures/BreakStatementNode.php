<?php
namespace Pharborist\ControlStructures;

use Pharborist\StatementNode;
use Pharborist\IntegerNode;

/**
 * A break statement.
 */
class BreakStatementNode extends StatementNode {
  /**
   * @var IntegerNode
   */
  protected $level;

  /**
   * An optional numeric argument which tells break how many nested enclosing
   * structures are to be broken out of.
   * @return IntegerNode
   */
  public function getLevel() {
    return $this->level;
  }
}
