<?php
namespace Pharborist\ControlStructures;

use Pharborist\ParentNode;
use Pharborist\ExpressionNode;

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
