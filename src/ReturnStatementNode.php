<?php
namespace Pharborist;

/**
 * A return statement.
 */
class ReturnStatementNode extends StatementNode {
  protected $properties = array(
    'value' => NULL,
  );

  /**
   * An optional value to return.
   * @return ExpressionNode
   */
  public function getValue() {
    return $this->properties['value'];
  }
}
