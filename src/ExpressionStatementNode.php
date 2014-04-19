<?php
namespace Pharborist;

/**
 * An expression statement.
 *
 * For example, expr();
 */
class ExpressionStatementNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var ExpressionNode
   */
  protected $expression;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return ExpressionNode
   */
  public function getExpression() {
    return $this->expression;
  }
}
