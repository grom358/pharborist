<?php
namespace Pharborist\Objects;

use Pharborist\CommaListNode;
use Pharborist\Functions\CallNode;
use Pharborist\Namespaces\NameNode;
use Pharborist\Node;
use Pharborist\Token;
use Pharborist\Variables\VariableExpressionNode;

/**
 * A call to a static class method, e.g. `MyClass::classMethod()`
 */
class ClassMethodCallNode extends CallNode implements VariableExpressionNode {
  /**
   * @var \Pharborist\Namespaces\NameNode|Node
   */
  protected $className;

  /**
   * @var Node
   */
  protected $methodName;

  /**
   * @return \Pharborist\Namespaces\NameNode|Node
   */
  public function getClassName() {
    return $this->className;
  }

  /**
   * @param string|Node $class_name
   * @return $this
   */
  public function setClassName($class_name) {
    if (is_string($class_name)) {
      $class_name = Token::identifier($class_name);
    }
    $this->className->replaceWith($class_name);
    $this->className = $class_name;
    return $this;
  }

  /**
   * @return Node
   */
  public function getMethodName() {
    return $this->methodName;
  }

  /**
   * @param string|Node $method_name
   * @return $this
   */
  public function setMethodName($method_name) {
    if (is_string($method_name)) {
      $method_name = Token::identifier($method_name);
    }
    $this->methodName->replaceWith($method_name);
    $this->methodName = $method_name;
    return $this;
  }

  /**
   * Creates a method call on a class with an empty argument list.
   *
   * @param Node|string $class_name
   *  The class node which is typically NameNode of class.
   * @param string $method_name
   *  The name of the called method.
   *
   * @return static
   */
  public static function create($class_name, $method_name) {
    if (is_string($class_name)) {
      $class_name = NameNode::create($class_name);
    }
    /** @var ClassMethodCallNode $node */
    $node = new static();
    $node->addChild($class_name, 'className');
    $node->addChild(Token::doubleColon());
    $node->addChild(Token::identifier($method_name), 'methodName');
    $node->addChild(Token::openParen(), 'openParen');
    $node->addChild(new CommaListNode(), 'arguments');
    $node->addChild(Token::closeParen(), 'closeParen');
    return $node;
  }
}
