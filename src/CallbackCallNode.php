<?php
namespace Pharborist;

/**
 * A callback call.
 *
 * For example, $callback().
 */
class CallbackCallNode extends CallNode implements ExpressionNode {
  protected $properties = array(
    'functionReference' => NULL,
    'arguments' => array(),
  );

  /**
   * @return Node
   */
  public function getFunctionReference() {
    return $this->properties['functionReference'];
  }

  public function getArguments() {
    return $this->properties['arguments'];
  }
}
