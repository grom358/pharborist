<?php
namespace Pharborist\Functions;

use Pharborist\Namespaces\NameNode;
use Pharborist\Variables\VariableExpressionNode;

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

  /**
   * @param string|\Pharborist\Namespaces\NameNode $name
   * @return $this
   */
  public function setName($name) {
    if (is_string($name)) {
      $name = NameNode::create($name);
    }
    $this->name->replaceWith($name);
    $this->name = $name;
    return $this;
  }
}
