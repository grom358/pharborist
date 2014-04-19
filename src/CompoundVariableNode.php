<?php
namespace Pharborist;

/**
 * A compound variable.
 *
 * For example, ${expr()}
 */
class CompoundVariableNode extends ParentNode implements VariableExpressionNode {
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
