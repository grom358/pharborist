<?php
namespace Pharborist\Exceptions;

use Pharborist\StatementNode;
use Pharborist\ExpressionNode;

/**
 * A throw statement.
 */
class ThrowStatementNode extends StatementNode {
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
