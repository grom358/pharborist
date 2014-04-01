<?php
namespace Pharborist;

/**
 * Constant declaration statement.
 */
class ConstantDeclarationStatementNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var ConstantDeclarationNode[]
   */
  public $declarations = array();
}
