<?php
namespace Pharborist;

/**
 * A require.
 */
class RequireNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $expression;
}
