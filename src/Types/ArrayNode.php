<?php
namespace Pharborist\Types;

use Pharborist\ParentNode;
use Pharborist\ParenTrait;
use Pharborist\Token;
use Pharborist\ExpressionNode;
use Pharborist\Filter;
use Pharborist\CommaListNode;
use Pharborist\NodeCollection;
use Pharborist\Parser;

/**
 * A PHP array, e.g. `array(1, 3, 'banana', 'apple')`
 */
class ArrayNode extends ParentNode implements ExpressionNode {
  use ParenTrait;

  /**
   * @var CommaListNode
   */
  protected $elements;

  /**
   * @return CommaListNode
   */
  public function getElementList() {
    return $this->elements;
  }

  /**
   * @return NodeCollection|ArrayElementNode[]
   */
  public function getElements() {
    return $this->elements->getItems();
  }

  /**
   * Tests if the array contains another array.
   *
   * @return boolean
   */
  public function isMultidimensional() {
    foreach ($this->elements->getItems() as $element) {
      if ($element instanceof ArrayPairNode) {
        if ($element->getValue() instanceof ArrayNode) {
          return TRUE;
        }
      }
      elseif ($element instanceof ArrayNode) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Convert to PHP array.
   *
   * @return array
   *   Array of scalars.
   *
   * @throws \BadMethodCallException
   *   Thrown if array contains non scalar elements.
   */
  public function toValue() {
    $ret = array();
    foreach ($this->elements->getItems() as $element) {
      if ($element instanceof ArrayNode) {
        $ref[] = $element->toValue();
      }
      elseif ($element instanceof ArrayPairNode) {
        $key = $element->getKey();
        $value = $element->getValue();
        $value_convertable = $value instanceof ScalarNode || $value instanceof ArrayNode;
        if (!($key instanceof ScalarNode && $value_convertable)) {
          throw new \BadMethodCallException('Can only convert scalar arrays.');
        }
        $ret[$key->toValue()] = $value->toValue();
      }
      elseif ($element instanceof ScalarNode || $element instanceof ArrayNode) {
        /** @var ScalarNode|ArrayNode $element */
        $ret[] = $element->toValue();
      }
      else {
        throw new \BadMethodCallException('Can only convert scalar arrays.');
      }
    }
    return $ret;
  }

  /**
   * Returns if the array has a specific key.
   *
   * @param mixed $key
   *  Either a scalar value ('foo') or an ExpressionNode representing the key.
   *  If $key is an ExpressionNode, the key's string representation is compared
   *  with the string representations of the array keys. Otherwise, the actual
   *  value is compared.
   * @param boolean $recursive
   *  Whether or not to check every level of the array.
   *
   * @return boolean
   *
   * @throws \InvalidArgumentException
   */
  public function hasKey($key, $recursive = TRUE) {
    if (!($key instanceof ExpressionNode) && !is_scalar($key)) {
      throw new \InvalidArgumentException();
    }

    $keys = $this->getKeys($recursive);
    if (is_scalar($key)) {
      return $keys
        ->filter(Filter::isInstanceOf('\Pharborist\Types\ScalarNode'))
        ->is(function(ScalarNode $node) use ($key) {
          return $node->toValue() === $key;
        });
    }
    else {
      return $keys
        ->is(function(ExpressionNode $expr) use ($key) {
          return $expr->getText() === $key->getText();
        });
    }
  }

  /**
   * Get the keys of the array.
   *
   * @param boolean $recursive
   *   (optional) TRUE to get keys of array elements that are also arrays.
   *
   * @return NodeCollection
   */
  public function getKeys($recursive = TRUE) {
    $keys = new NodeCollection();
    $index = 0;
    foreach ($this->elements->getItems() as $element) {
      if ($element instanceof ArrayPairNode) {
        $keys->add($element->getKey());
        $value = $element->getValue();
      }
      else {
        $keys->add(Token::integer($index++));
        $value = $element;
      }

      if ($recursive && $value instanceof ArrayNode) {
        $keys->add($value->getKeys($recursive));
      }
    }
    return $keys;
  }

  /**
   * Get the values of the array.
   *
   * @param boolean $recursive
   *   (optional) TRUE to get values of array elements that are also arrays.
   *
   * @return NodeCollection
   */
  public function getValues($recursive = TRUE) {
    $values = new NodeCollection();
    foreach ($this->elements->getItems() as $element) {
      if ($element instanceof ArrayPairNode) {
        $value = $element->getValue();
        if ($recursive && $value instanceof ArrayNode) {
          $values->add($value->getValues($recursive));
        }
        else {
          $values->add($value);
        }
      }
      else {
        $values->add($element);
      }
    }
    return $values;
  }

  /**
   * @param ArrayElementNode[] $elements
   *   Array elements.
   *
   * @return ArrayNode
   */
  public static function create($elements) {
    /** @var ArrayNode $node */
    $node = Parser::parseExpression('[]');
    /** @var \Pharborist\Node $element */
    foreach ($elements as $element) {
      $node->getElementList()->appendItem($element);
    }
    return $node;
  }
}
