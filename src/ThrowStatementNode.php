<?php
namespace Pharborist;

/**
 * A throw statement.
 */
class ThrowStatementNode extends StatementNode {
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
