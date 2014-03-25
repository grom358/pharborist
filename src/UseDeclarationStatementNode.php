<?php
namespace Pharborist;

/**
 * Use declaration statement.
 */
class UseDeclarationStatementNode extends StatementNode {
  /**
   * @var UseDeclarationNode[]
   */
  public $declarations = array();
}
