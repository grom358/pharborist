<?php
namespace Pharborist;

/**
 * A dynamic function/method call.
 *
 * For example, $callback().
 */
class DynamicCallNode extends ParentNode implements ExpressionNode {
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
