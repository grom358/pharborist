<?php
namespace Pharborist;

/**
 * A break statement.
 */
class BreakStatementNode extends StatementNode {
  protected $properties = array(
    'level' => NULL,
  );

  /**
   * An optional numeric argument which tells break how many nested enclosing
   * structures are to be broken out of.
   * @return IntegerNode
   */
  public function getLevel() {
    return $this->properties['level'];
  }
}
