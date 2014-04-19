<?php
namespace Pharborist;

/**
 * A parenthesis expression.
 */
class ParenthesisNode extends ParentNode implements ExpressionNode {
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
