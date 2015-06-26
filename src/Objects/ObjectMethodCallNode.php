<?php
namespace Pharborist\Objects;

use Pharborist\CommaListNode;
use Pharborist\Functions\CallNode;
use Pharborist\Node;
use Pharborist\Token;
use Pharborist\TokenNode;
use Pharborist\Variables\VariableExpressionNode;

/**
 * An object method call, e.g. `$object->method()`
 */
class ObjectMethodCallNode extends CallNode implements VariableExpressionNode {
  /**
   * @var Node
   */
  protected $object;

  /**
   * @var TokenNode
   */
  protected $operator;

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
   * The object operator '->' token (T_OBJECT_OPERATOR).
   *
   * @return TokenNode
   */
  public function getOperator() {
    return $this->operator;
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
    $node->addChild(Token::objectOperator(), 'operator');
    $node->addChild(Token::identifier($method_name), 'methodName');
    $node->addChild(Token::openParen(), 'openParen');
    $node->addChild(new CommaListNode(), 'arguments');
    $node->addChild(Token::closeParen(), 'closeParen');
    return $node;
  }

  /**
   * If this is a chained method call (e.g., foo()->bar()->baz()), returns
   * the previous call in the chain.
   *
   * @return \Pharborist\Functions\CallNode|NULL
   *   The previous call in the chain or NULL if there is none.
   */
  public function getPreviousCall() {
    if ($this->object instanceof CallNode) {
      return $this->object;
    }
    else {
      return NULL;
    }
  }
}
