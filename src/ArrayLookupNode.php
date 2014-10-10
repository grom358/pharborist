<?php
namespace Pharborist;

/**
 * An array lookup.
 *
 * For example $array[0]
 */
class ArrayLookupNode extends ParentNode implements VariableExpressionNode {
  /**
   * @var Node
   */
  protected $array;

  /**
   * @var Node
   */
  protected $key;

  /**
   * Creates a new array lookup.
   *
   * @param \Pharborist\ExpressionNode $array
   *  The expression representing the array (usually a VariableNode).
   * @param \Pharborist\ExpressionNode $key
   *  The expression representing the key (usually a string).
   *
   * @return static
   */
  public static function create(ExpressionNode $array, ExpressionNode $key) {
    $node = new static();
    $node->addChild($array, 'array');
    $node->addChild(Token::openBracket());
    $node->addChild($key, 'key');
    $node->addChild(Token::closeBracket());
    return $node;
  }

  /**
   * @return Node
   */
  public function getArray() {
    return $this->array;
  }

  /**
   * @return Node
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * Returns TRUE if all keys in the lookup are scalar. So a lookup like
   * $foo['bar']['baz'][0] will be TRUE, but $foo[$bar]['baz'][0] won't.
   *
   * @return boolean
   */
  public function hasScalarKeys() {
    if ($this->key instanceof ScalarNode) {
      return $this->array instanceof ArrayLookupNode ? $this->array->hasScalarKeys() : TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Returns every key in the lookup. For example, $foo['bar']['baz'][5] will
   * return ['bar', 'baz', 5].
   *
   * @return mixed[]
   *
   * @throws \DomainException if the lookup contains any non-scalar keys.
   */
  public function extractKeys() {
    if (!$this->hasScalarKeys()) {
      throw new \DomainException('Cannot extract non-scalar keys from array lookup ' . $this);
    }
    $keys = [ $this->key->toValue() ];
    if ($this->array instanceof ArrayLookupNode) {
      $keys = array_merge($this->array->extractKeys(), $keys);
    }
    return $keys;
  }
}
