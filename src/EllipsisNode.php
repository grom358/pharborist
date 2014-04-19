<?php
namespace Pharborist;

/**
 * Ellipsis parameter.
 *
 * For example, a_func($a, ...$b);
 */
class EllipsisNode extends ParentNode {
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
