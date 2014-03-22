<?php
namespace Pharborist;

/**
 * Use declaration statement.
 * @package Pharborist
 */
class UseDeclarationStatementNode extends StatementNode {
  /**
   * @var UseDeclarationNode[]
   */
  public $declarations = array();
}
