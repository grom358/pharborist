<?php
namespace Pharborist;

/**
 * An if control structure.
 */
class IfNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  public $condition;

  /**
   * @var Node
   */
  public $then;

  /**
   * @var ElseIfNode[]
   */
  public $elseIfList = array();

  /**
   * @var Node
   */
  public $else;
}
