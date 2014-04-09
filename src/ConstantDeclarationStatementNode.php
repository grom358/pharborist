<?php
namespace Pharborist;

/**
 * Constant declaration statement.
 */
class ConstantDeclarationStatementNode extends StatementNode {
  protected $properties = array(
    'declarations' => array(),
  );

  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @return ConstantDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->properties['declarations'];
  }
}
