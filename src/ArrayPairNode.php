<?php
namespace Pharborist;

/**
 * A key/value pair element in php array.
 */
class ArrayPairNode extends ParentNode implements ArrayElementNode {
  /**
   * @var ExpressionNode
   */
  protected $key;

  /**
   * @var ExpressionNode
   */
  protected $value;

  /**
   * @return ExpressionNode
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * Sets the key of the pair.
   *
   * @param mixed $key
   *  The key to set. Can be a scalar or an ExpressionNode.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   */
  public function setKey($key) {
    if (is_scalar($key)) {
      $key = Node::fromValue($key);
    }
    if (!($value instanceof ExpressionNode)) {
      throw new \InvalidArgumentException();
    }
    $this->key->replaceWith($key);
    return $this->key;
  }

  /**
   * @return ExpressionNode
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * Sets the value of the pair.
   *
   * @param mixed $value
   *  The value to set. Can be a scalar or an ExpressionNode.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   */
  public function setValue($value) {
    if (is_scalar($value)) {
      $value = Node::fromValue($value);
    }
    if (!($value instanceof ExpressionNode)) {
      throw new \InvalidArgumentException();
    }
    $this->value->replaceWith($value);
    return $this;
  }

  /**
   * @param ExpressionNode $key
   *   Array element's key.
   * @param ExpressionNode $value
   *   Array element's value.
   *
   * @return ArrayPairNode
   */
  public static function create(ExpressionNode $key, ExpressionNode $value) {
    $node = new ArrayPairNode();
    $node->addChild($key, 'key');
    $node->addChild(Token::space());
    $node->addChild(Token::doubleArrow());
    $node->addChild(Token::space());
    $node->addChild($value, 'value');
    return $node;
  }
}
