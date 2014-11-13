<?php
namespace Pharborist;

use Pharborist\Types\ScalarNode;
use Pharborist\Variables\VariableExpressionNode;

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
    /** @var Node $array */
    $node->addChild($array, 'array');
    $node->addChild(Token::openBracket());
    /** @var Node $key */
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
   * @return \Pharborist\Node[]
   */
  public function getKeys() {
    $keys = [];

    if ($this->key) {
      $keys[] = clone $this->key;
    }
    if ($this->array instanceof ArrayLookupNode) {
      $keys = array_merge($this->array->getKeys(), $keys);
    }

    return $keys;
  }

  /**
   * Returns a specific key in the lookup.
   *
   * @param integer $index
   *  The index of the key to return.
   *
   * @return \Pharborist\Node
   *
   * @throws
   *  \InvalidArgumentException if $index is not an integer.
   *  \OutOfBoundsException if $index is less than zero or greater than the
   *  number of keys in the lookup.
   */
  public function getKey($index = 0) {
    $keys = $this->getKeys();

    if (!is_integer($index)) {
      throw new \InvalidArgumentException();
    }
    if ($index < 0 || $index >= count($keys)) {
      throw new \OutOfBoundsException();
    }
    return $keys[$index];
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
    return array_map(function(ScalarNode $key) { return $key->toValue(); }, $this->getKeys());
  }

  /**
   * Returns the root of the lookup.
   *
   * For example, given an expression like $foo['bar']['baz'],
   * this method will return a VariableNode for $foo.
   *
   * @return Node
   */
  public function getRootArray() {
    return $this->array instanceof ArrayLookupNode ? $this->array->getRootArray() : $this->array;
  }
}
