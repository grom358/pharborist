<?php
namespace Pharborist;

/**
 * A ternary operation.
 *
 * For example, $condition ? $then : $else
 */
class TernaryOperationNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $then;

  /**
   * @var Node
   */
  public $else;
}
