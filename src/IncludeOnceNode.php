<?php
namespace Pharborist;

/**
 * An include_once.
 */
class IncludeOnceNode extends ParentNode implements ExpressionNode {
  /**
   * @var ExpressionNode
   */
  public $expression;
}
