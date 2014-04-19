<?php
namespace Pharborist;

/**
 * A static variable statement.
 *
 * For example, static $a, $b = A_SCALAR;
 */
class StaticVariableStatementNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return StaticVariableNode[]
   */
  public function getVariables() {
    return $this->childrenByInstance('\Pharborist\StaticVariableNode');
  }
}
