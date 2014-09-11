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
  public function appendMethodCall($method_name) {
    $method_call = ObjectMethodCallNode::create(clone $this, $method_name);
    $this->replaceWith($method_call);
    return $method_call;
  }
}
