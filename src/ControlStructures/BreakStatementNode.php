<?php
namespace Pharborist\ControlStructures;

use Pharborist\ParenTrait;
use Pharborist\StatementNode;
use Pharborist\Types\IntegerNode;

/**
 * A break statement.
 */
class BreakStatementNode extends StatementNode {
  use ParenTrait;

  /**
   * @var IntegerNode
   */
  protected $level;

  /**
   * An optional numeric argument which tells break how many nested enclosing
   * structures are to be broken out of.
   * @return \Pharborist\Types\IntegerNode
   */
  public function getLevel() {
    return $this->level;
  }
}
