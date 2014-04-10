<?php
namespace Pharborist;

/**
 * A callback call.
 *
 * For example, $callback().
 */
class CallbackCallNode extends CallNode implements ExpressionNode {
  protected $properties = array(
    'callback' => NULL,
    'arguments' => array(),
  );

  /**
   * @return Node
   */
  public function getCallback() {
    return $this->properties['callback'];
  }

  public function getArguments() {
    return $this->properties['arguments'];
  }
}
