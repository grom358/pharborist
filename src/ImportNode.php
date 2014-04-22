<?php
namespace Pharborist;

/**
 * An include(_once) or require(_once) expression.
 */
abstract class ImportNode extends ParentNode implements ExpressionNode {
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
