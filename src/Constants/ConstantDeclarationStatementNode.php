<?php

/**
 * @file
 * Contains \Pharborist\Constants\ConstantDeclarationStatementNode.
 */

namespace Pharborist\Constants;

use Pharborist\ClassStatementNode;
use Pharborist\DocCommentTrait;
use Pharborist\InterfaceStatementNode;

/**
 * Constant declaration statement.
 */
class ConstantDeclarationStatementNode extends ClassStatementNode implements InterfaceStatementNode {
  use DocCommentTrait;

  /**
   * @return ConstantDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->childrenByInstance('\Pharborist\Constants\ConstantDeclarationNode');
  }
}
