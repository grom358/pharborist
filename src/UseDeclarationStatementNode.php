<?php
namespace Pharborist;

/**
 * Use declaration statement.
 */
class UseDeclarationStatementNode extends StatementNode {
  /**
   * @return UseDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->childrenByInstance('\Pharborist\UseDeclarationNode');
  }
}
