<?php

/**
 * @file
 * Contains \Pharborist\FunctionCallNode.
 */

namespace Pharborist;

use Pharborist\Functions\CallNode;

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
   * @param string|NameNode $name
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
