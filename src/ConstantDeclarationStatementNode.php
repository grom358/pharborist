<?php
namespace Pharborist;

/**
 * Constant declaration statement.
 */
class ConstantDeclarationStatementNode extends ClassStatementNode implements InterfaceStatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return ConstantDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->childrenByInstance('\Pharborist\ConstantDeclarationNode');
  }
}
