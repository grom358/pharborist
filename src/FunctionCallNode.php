<?php
namespace Pharborist;

/**
 * A function call.
 */
class FunctionCallNode extends CallNode implements VariableExpressionNode {
  /**
   * @var NamespacePathNode
   */
  protected $namespacePath;

  /**
   * @var ArgumentListNode
   */
  protected $arguments;

  /**
   * @return NamespacePathNode
   */
  public function getNamespacePath() {
    return $this->namespacePath;
  }

  public function getArguments() {
    return $this->arguments->getArguments();
  }
}
