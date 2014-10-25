<?php
namespace Pharborist\ControlStructures;

use Pharborist\Node;
use Pharborist\StatementNode;
use Pharborist\ExpressionNode;

/**
 * while control structure.
 */
class WhileNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @var Node
   */
  protected $body;

  /**
   * @return ExpressionNode
   */
  public function getCondition() {
    return $this->condition;
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->body;
  }
}
