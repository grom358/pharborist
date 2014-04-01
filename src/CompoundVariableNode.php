<?php
namespace Pharborist;

/**
 * A compound variable.
 *
 * For example, ${expr()}
 */
class CompoundVariableNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $expression;
}
