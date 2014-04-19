<?php
namespace Pharborist;

/**
 * A block of statements.
 */
class StatementBlockNode extends ParentNode {
  /**
   * @return StatementNode[]
   */
  public function getStatements() {
    return $this->childrenByInstance('\Pharborist\StatementNode');
  }
}
