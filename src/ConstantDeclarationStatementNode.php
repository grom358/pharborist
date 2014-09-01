<?php
namespace Pharborist;

/**
 * Constant declaration statement.
 */
class ConstantDeclarationStatementNode extends ClassStatementNode implements InterfaceStatementNode {
  use DocCommentTrait;

  /**
   * @return ConstantDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->childrenByInstance('\Pharborist\ConstantDeclarationNode');
  }
}
