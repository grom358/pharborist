<?php
namespace Pharborist;

/**
 * A callback call.
 *
 * For example, $callback().
 */
class CallbackCallNode extends CallNode implements ExpressionNode {
  /**
   * @var Node
   */
  protected $callback;

  /**
   * @return Node
   */
  public function getCallback() {
    return $this->callback;
  }
}
