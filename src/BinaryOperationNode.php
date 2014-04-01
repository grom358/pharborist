<?php
namespace Pharborist;

/**
 * A binary operation.
 */
abstract class BinaryOperationNode extends ParentNode implements ExpressionNode {
  /**
   * @var ExpressionNode
   */
  public $left;

  /**
   * @var Node
   */
  public $operator;

  /**
   * @var ExpressionNode
   */
  public $right;
}
