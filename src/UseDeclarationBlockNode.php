<?php
namespace Pharborist;

/**
 * A set of use statements.
 *
 * Example:
 * ```
 * use \JohnCleese;
 * use \EricIdle;
 * use \TerryGilliam as TerryJones;
 * use \MichaelPalin;
 * ```
 */
class UseDeclarationBlockNode extends StatementBlockNode {
  /**
   * @return UseDeclarationStatementNode[]
   */
  public function getDeclarationStatements() {
    return $this->childrenByInstance('\Pharborist\UseDeclarationStatementNode');
  }
}
