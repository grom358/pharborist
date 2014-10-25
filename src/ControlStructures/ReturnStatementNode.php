<?php
namespace Pharborist\ControlStructures;

use Pharborist\StatementNode;
use Pharborist\Parser;
use Pharborist\ExpressionNode;

/**
 * A return statement.
 */
class ReturnStatementNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  protected $expression;

  /**
   * Creates a new return statement.
   *
   * @param \Pharborist\ExpressionNode $expr
   *  The expression to return.
   *
   * @return static
   */
  public static function create(ExpressionNode $expr) {
    return Parser::parseSnippet('return ' . $expr->getText() . ';');
  }

  /**
   * @return ExpressionNode
   */
  public function getExpression() {
    return $this->expression;
  }
}
