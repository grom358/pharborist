<?php
namespace Pharborist;

/**
 * Splat operator, e.g. `a_func($a, ...$b)`
 *
 * Arrays and Traversable objects can be unpacked into argument lists when
 * calling functions by using the ... operator. This is also known as the
 * splat operator.
 */
class SplatNode extends ParentNode {
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
