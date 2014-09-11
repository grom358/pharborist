<?php
namespace Pharborist;

/**
 * An object method call.
 *
 * For example, $object->method()
 */
class ObjectMethodCallNode extends CallNode implements VariableExpressionNode {
  /**
   * @var Node
   */
  protected $object;

  /**
   * @var Node
   */
  protected $methodName;

  /**
   * @return Node
   */
  public function getObject() {
    return $this->object;
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
   * Creates a method call on an object with an empty argument list.
   *
   * @param Node $object
   *  The expression that is an object.
   * @param string $method_name
   *  The name of the called method.
   *
   * @return static
   */
  public static function create(Node $object, $method_name) {
    /** @var ObjectMethodCallNode $node */
    $node = new static();
    $node->addChild($object, 'object');
    $node->addChild(Token::objectOperator());
    $node->addChild(NameNode::create($method_name), 'methodName');
    $node->addChild(Token::openParen());
    $node->addChild(new CommaListNode(), 'arguments');
    $node->addChild(Token::closeParen());
    return $node;
  }
}
