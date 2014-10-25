<?php
namespace Pharborist\ControlStructures;

use Pharborist\Node;
use Pharborist\StatementNode;
use Pharborist\ExpressionNode;

/**
 * An if control structure.
 */
class IfNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @var Node
   */
  protected $then;

  /**
   * @var Node
   */
  protected $else;

  /**
   * @return ExpressionNode
   */
  public function getCondition() {
    return $this->condition;
  }

  /**
   * @return Node
   */
  public function getThen() {
    return $this->then;
  }

  /**
   * @return ElseIfNode[]
   */
  public function getElseIfs() {
    return $this->childrenByInstance('\Pharborist\ControlStructures\ElseIfNode');
  }

  /**
   * @return Node
   */
  public function getElse() {
    return $this->else;
  }
}
