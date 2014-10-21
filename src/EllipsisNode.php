<?php
namespace Pharborist;

/**
 * A variadic function parameter, e.g. `a_func($a, ...$b)`
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
