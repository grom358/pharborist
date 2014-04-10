<?php
namespace Pharborist;

/**
 * A callback call.
 *
 * For example, $callback().
 */
class CallbackCallNode extends ParentNode implements ExpressionNode {
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

  /**
   * @return ExpressionNode[]
   */
  public function getArguments() {
    return $this->properties['arguments'];
  }
}
