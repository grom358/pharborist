<?php
namespace Pharborist;

/**
 * A require_once.
 */
class RequireOnceNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $expression;
}
