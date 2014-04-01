<?php
namespace Pharborist;

/**
 * A variable variable.
 *
 * For example, $$a
 */
class VariableVariableNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $variable;
}
