<?php
namespace Pharborist;

/**
 * A continue statement.
 */
class ContinueStatementNode extends StatementNode {
  protected $properties = array(
    'level' => NULL,
  );

  /**
   * An optional numeric argument which tells continue how many
   * enclosing structures are to be skipped to the end of.
   * @return IntegerNode
   */
  public function getLevel() {
    return $this->properties['level'];
  }
}
