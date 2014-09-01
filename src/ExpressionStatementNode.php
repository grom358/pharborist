<?php
namespace Pharborist;

/**
 * An expression statement.
 *
 * For example, expr();
 */
class ExpressionStatementNode extends StatementNode {
  use DocCommentTrait;

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
