<?php
namespace Pharborist;

/**
 * Use declaration statement.
 */
class UseDeclarationStatementNode extends StatementNode {
  protected $properties = array(
    'declarations' => array(),
  );

  /**
   * @return UseDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->properties['declarations'];
  }
}
