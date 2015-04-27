<?php
namespace Pharborist\Functions;

use Pharborist\Objects\ObjectMethodCallNode;
use Pharborist\ParentNode;
use Pharborist\ParenTrait;

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
  use ParenTrait;

  /**
   * Allows you to append a method call to this one, building a chain of method
   * calls.
   *
   * For example:
   * ```
   * // \Drupal::entityManager()
   * $classCall = ClassMethodCallNode::create('\Drupal', 'entityManager');
   *
   * $methodCall = $classCall->appendMethodCall('getDefinitions');
   * echo $methodCall->getText(); // \Drupal::entityManager()->getDefinitions()
   * echo $methodCall->getObject(); // \Drupal::entityManager()
   * echo $methodCall->getMethodName(); // getDefinitions
   *
   * // You can chain yet another call, and keep going as long as you want.
   *
   * $methodCall = $methodCall->appendMethodCall('clearCache')
   * echo $methodCall->getText(); // \Drupal::entityManager()->getDefinitions()->clearCache()
   *
   * // These methods are chainable themselves, so you can build an entire call chain
   * // in one fell swoop.
   *
   * $chain = ClassMethodCallNode::create('Foo', 'bar')->appendMethodCall('baz')->appendMethodCall('zorg');
   * echo $chain->getText();  // Foo::bar()->baz()->zorg()
   * ```
   *
   * @param string $method_name
   *  The name of the method to call.
   *
   * @return \Pharborist\Objects\ObjectMethodCallNode
   *  The newly-created method call, in which every previous part of the chain will be the
   *  "object", and $method_name will be the "method". The call will be created without
   *  arguments, but you can add some using appendArgument().
   */
  public function appendMethodCall($method_name) {
    $method_call = ObjectMethodCallNode::create(clone $this, $method_name);
    $this->replaceWith($method_call);
    return $method_call;
  }
}
