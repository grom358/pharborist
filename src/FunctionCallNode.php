<?php
namespace Pharborist;

/**
 * A function call.
 */
class FunctionCallNode extends CallNode implements VariableExpressionNode {
  protected $properties = array(
    'namespacePath' => NULL,
    'arguments' => NULL,
  );

  /**
   * @return NamespacePathNode
   */
  public function getNamespacePath() {
    return $this->properties['namespacePath'];
  }

  public function getArguments() {
    /** @var ArgumentListNode $arguments */
    $arguments = $this->properties['arguments'];
    return $arguments->getArguments();
  }
}
