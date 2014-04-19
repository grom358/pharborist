<?php
namespace Pharborist;

/**
 * An exit.
 */
class ExitNode extends ParentNode implements ExpressionNode {
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
