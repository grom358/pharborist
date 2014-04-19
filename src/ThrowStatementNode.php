<?php
namespace Pharborist;

/**
 * A throw statement.
 */
class ThrowStatementNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  protected $expression;

  /**
   * @return ExpressionNode
   */
  public function getExpression() {
    return $this->expression;
  }
}
