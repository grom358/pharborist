<?php
namespace Pharborist;

/**
 * do while control structure.
 */
class DoWhileNode extends StatementNode {
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
