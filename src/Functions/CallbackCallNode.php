<?php
namespace Pharborist\Functions;

use Pharborist\ExpressionNode;
use Pharborist\Node;

/**
 * A call to a callback function in a variable, e.g. `$foo = $callback('baz');`
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
