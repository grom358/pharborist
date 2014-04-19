<?php
namespace Pharborist;

/**
 * A static variable statement.
 *
 * For example, static $a, $b = A_SCALAR;
 */
class StaticVariableStatementNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }

  /**
   * @return StaticVariableNode[]
   */
  public function getVariables() {
    return $this->childrenByInstance('\Pharborist\StaticVariableNode');
  }
}
