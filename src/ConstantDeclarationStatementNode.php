<?php
namespace Pharborist;

/**
 * Constant declaration statement.
 */
class ConstantDeclarationStatementNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }

  /**
   * @return ConstantDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->childrenByInstance('\Pharborist\ConstantDeclarationNode');
  }
}
