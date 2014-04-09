<?php
namespace Pharborist;

/**
 * A block of statements.
 */
class StatementBlockNode extends ParentNode {
  protected $properties = array(
    'statements' => array(),
  );

  /**
   * @return StatementNode[]
   */
  public function getStatements() {
    return $this->properties['statements'];
  }
}
