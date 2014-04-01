<?php
namespace Pharborist;

/**
 * do while control structure.
 */
class DoWhileNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  public $condition;

  /**
   * @var Node
   */
  public $body;
}
