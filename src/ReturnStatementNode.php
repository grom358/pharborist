<?php
namespace Pharborist;

/**
 * A return statement.
 */
class ReturnStatementNode extends StatementNode {
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
