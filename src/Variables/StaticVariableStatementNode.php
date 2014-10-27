<?php
namespace Pharborist\Variables;

use Pharborist\NodeCollection;
use Pharborist\StatementNode;
use Pharborist\DocCommentTrait;
use Pharborist\CommaListNode;

/**
 * A static variable statement.
 *
 * For example, static $a, $b = A_SCALAR;
 */
class StaticVariableStatementNode extends StatementNode {
  use DocCommentTrait;

  /**
   * @var CommaListNode
   */
  protected $variables;

  /**
   * @return CommaListNode
   */
  public function getVariableList() {
    return $this->variables;
  }

  /**
   * @return NodeCollection|StaticVariableNode[]
   */
  public function getVariables() {
    return $this->variables->getItems();
  }
}
