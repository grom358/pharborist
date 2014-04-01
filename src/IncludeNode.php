<?php
namespace Pharborist;

/**
 * An include.
 */
class IncludeNode extends ParentNode implements ExpressionNode {
  /**
   * @var ExpressionNode
   */
  public $expression;
}
