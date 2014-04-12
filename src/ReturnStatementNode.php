<?php
namespace Pharborist;

/**
 * A return statement.
 */
class ReturnStatementNode extends StatementNode {
  protected $properties = array(
    'expression' => NULL,
  );

  /**
   * @return ExpressionNode
   */
  public function getExpression() {
    return $this->properties['expression'];
  }
}
