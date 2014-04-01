<?php
namespace Pharborist;

/**
 * while control structure.
 */
class WhileNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  public $condition;

  /**
   * @var Node
   */
  public $body;
}
