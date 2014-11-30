<?php
namespace Pharborist;

/**
 * Any expression enclosed by parentheses, e.g. `(($a && $b) || ($a && $c))`
 */
class ParenthesisNode extends ParentNode implements ExpressionNode {
  use ParenTrait;

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
