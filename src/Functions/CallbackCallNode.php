<?php
namespace Pharborist\Functions;

use Pharborist\ExpressionNode;
use Pharborist\Node;

/**
 * A call to a callback function, e.g. `$callback('baz')`
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
