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
   * @var ExpressionNode
   */
  public function getExpression() {
    return $this->expression;
  }
}
