<?php
namespace Pharborist;

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
    return Parser::parseSnippet('return ' . $expr . ';');
  }

  /**
   * @return ExpressionNode
   */
  public function getExpression() {
    return $this->expression;
  }
}
