<?php
namespace Pharborist;

/**
 * An exit.
 */
class ExitNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $status;
}
