<?php
namespace Pharborist\Namespaces;

use Pharborist\StatementBlockNode;

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
    return $this->childrenByInstance('\Pharborist\Namespaces\UseDeclarationStatementNode');
  }
}
