<?php
namespace Pharborist\ControlStructures;

use Pharborist\ParenTrait;
use Pharborist\StatementNode;
use Pharborist\Types\IntegerNode;

/**
 * A continue statement.
 */
class ContinueStatementNode extends StatementNode {
  use ParenTrait;

  /**
   * @var IntegerNode
   */
  protected $level;

  /**
   * An optional numeric argument which tells continue how many
   * enclosing structures are to be skipped to the end of.
   * @return IntegerNode
   */
  public function getLevel() {
    return $this->level;
  }
}
