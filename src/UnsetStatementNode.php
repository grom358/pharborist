<?php
namespace Pharborist;

/**
 * A unset statement.
 */
class UnsetStatementNode extends StatementNode {
  protected $properties = array(
    'functionCall' => NULL,
  );

  /**
   * @return UnsetNode
   */
  public function getFunctionCall() {
    return $this->properties['functionCall'];
  }
}
