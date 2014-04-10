<?php
namespace Pharborist;

/**
 * A function call.
 */
class FunctionCallNode extends CallNode implements ExpressionNode {
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
