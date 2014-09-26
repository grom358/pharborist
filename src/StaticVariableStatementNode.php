<?php
namespace Pharborist;

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
   * @return StaticVariableNode[]
   */
  public function getVariables() {
    return $this->variables->getItems();
  }
}
