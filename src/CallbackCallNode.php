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
    'arguments' => NULL,
  );

  /**
   * @return Node
   */
  public function getCallback() {
    return $this->properties['callback'];
  }

  /**
   * @return ExpressionNode[]
   */
  public function getArguments() {
    /** @var ArgumentListNode $arguments */
    $arguments = $this->properties['arguments'];
    return $arguments->getArguments();
  }
}
