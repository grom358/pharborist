<?php
namespace Pharborist;

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
