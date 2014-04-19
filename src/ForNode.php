<?php
namespace Pharborist;

/**
 * A for control structure.
 */
class ForNode extends StatementNode {
  /**
   * @var ExpressionListNode
   */
  protected $initial;

  /**
   * @var ExpressionListNode
   */
  protected $condition;

  /**
   * @var ExpressionListNode
   */
  protected $step;

  /**
   * @var Node
   */
  protected $body;

  /**
   * @return ExpressionListNode
   */
  public function getInitial() {
    return $this->initial;
  }

  /**
   * @return ExpressionListNode
   */
  public function getCondition() {
    return $this->condition;
  }

  /**
   * @return ExpressionListNode
   */
  public function getStep() {
    return $this->step;
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->body;
  }
}
