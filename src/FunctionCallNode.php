<?php
namespace Pharborist;

/**
 * A function call.
 */
class FunctionCallNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $functionReference;

  /**
   * @var ExpressionNode[]
   */
  public $arguments = array();
}
