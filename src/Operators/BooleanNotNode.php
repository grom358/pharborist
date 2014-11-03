<?php
namespace Pharborist\Operators;

use Pharborist\ExpressionNode;
use Pharborist\Token;

/**
 * A boolean '!' operation.
 */
class BooleanNotNode extends UnaryOperationNode {

  /**
   * Creates a negated version of any expression. For instance, passing a
   * VariableNode will result in !$var.
   *
   * @param \Pharborist\ExpressionNode $expr
   *  The expression to negate.
   *
   * @return static
   */
  public static function fromExpression(ExpressionNode $expr) {
    $not = new static();
    $not->addChild(Token::not(), 'operator');
    /** @var \Pharborist\Node $expr */
    $not->addChild($expr->remove(), 'operand');
    return $not;
  }

}
