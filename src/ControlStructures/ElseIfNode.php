<?php
namespace Pharborist\ControlStructures;

use Pharborist\ParentNode;
use Pharborist\Node;
use Pharborist\ExpressionNode;
use Pharborist\ParenTrait;

/**
 * elseif control structure.
 */
class ElseIfNode extends ParentNode {
  use ParenTrait;

  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @var Node
   */
  protected $then;

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
}
