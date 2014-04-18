<?php
namespace Pharborist;

/**
 * A function call.
 */
class FunctionCallNode extends CallNode implements VariableExpressionNode {
  protected $properties = array(
    'namespacePath' => NULL,
    'arguments' => array(),
  );

  /**
   * @return NamespacePathNode
   */
  public function getNamespacePath() {
    return $this->properties['namespacePath'];
  }

  public function getArguments() {
    return $this->properties['arguments'];
  }
}
