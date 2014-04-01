<?php
namespace Pharborist;

/**
 * elseif control structure.
 */
class ElseIfNode extends ParentNode {
  /**
   * @var ExpressionNode
   */
  public $condition;

  /**
   * @var Node
   */
  public $then;
}
