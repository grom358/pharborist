<?php
namespace Pharborist;

/**
 * A function call.
 */
class FunctionCallNode extends CallNode implements VariableExpressionNode {
  /**
   * @var NameNode
   */
  protected $name;

  /**
   * @return NameNode
   */
  public function getName() {
    return $this->name;
  }
}
