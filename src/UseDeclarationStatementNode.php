<?php
namespace Pharborist;

/**
 * Use declaration statement, importing several classes, functions, or constants
 * into a namespace.
 *
 * Example:
 * ```
 * use Jones, Gilliam, Cleese as Idle;
 * ```
 */
class UseDeclarationStatementNode extends StatementNode {
  /**
   * @return UseDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->childrenByInstance('\Pharborist\UseDeclarationNode');
  }
}
