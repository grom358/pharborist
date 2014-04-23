<?php
namespace Pharborist;

/**
 * A block of use declaration statements.
 */
class UseDeclarationBlockNode extends StatementBlockNode {
  /**
   * @return UseDeclarationStatementNode[]
   */
  public function getDeclarationStatements() {
    return $this->childrenByInstance('\Pharborist\UseDeclarationStatementNode');
  }
}
