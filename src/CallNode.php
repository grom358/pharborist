<?php
namespace Pharborist;

/**
 * Base class of any function or method call, including:
 *
 * ```
 * foobar();
 * $foo->bar();
 * Foo::bar();
 * $foo('bar');
 * ```
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
