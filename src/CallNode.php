<?php
namespace Pharborist;

/**
 * A function/method call.
 */
abstract class CallNode extends ParentNode {
  use ArgumentTrait;

  public function appendCall($method_name) {
    return ObjectMethodCallNode::create($this, $method_name);
  }
}
