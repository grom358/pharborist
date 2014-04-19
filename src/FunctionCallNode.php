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
   * @return NamespacePathNode
   */
  public function getNamespacePath() {
    return $this->namespacePath;
  }
}
