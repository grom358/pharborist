<?php
namespace Pharborist;

/**
 * A function call.
 */
class FunctionCallNode extends CallNode implements VariableExpressionNode {
  /**
   * @var NamespacePathNode
   */
  protected $name;

  /**
   * @return NamespacePathNode
   */
  public function getName() {
    return $this->name;
  }
}
