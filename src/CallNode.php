<?php
namespace Pharborist;

/**
 * A function/method call.
 */
abstract class CallNode extends ParentNode {
  use ArgumentTrait;

  /**
   * @param string $method_name
   *
   * @return ObjectMethodCallNode
   */
  public function appendCall($method_name) {
    return ObjectMethodCallNode::create($this, $method_name);
  }
}
