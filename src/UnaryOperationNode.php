<?php
namespace Pharborist;

/**
 * An unary operation.
 */
abstract class UnaryOperationNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $operator;

  /**
   * @var ExpressionNode
   */
  public $operand;
}
