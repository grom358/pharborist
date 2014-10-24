<?php
namespace Pharborist;

/**
 * A PHP array, e.g. `array(1, 3, 'banana', 'apple')`
 */
class ArrayNode extends ParentNode implements ExpressionNode {
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
   * @return ArrayElementNode[]
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
    return (boolean) $this->elements->children(Filter::isInstanceOf('Pharborist\ArrayNode'))->count();
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
        ->filter(Filter::isInstanceOf('\Pharborist\ScalarNode'))
        ->filter(function(ScalarNode $node) use ($key) {
          return $node->toValue() === $key;
        })
        ->count() > 0;
    }
    else {
      return $keys
        ->filter(function(ExpressionNode $expr) use ($key) {
          return $expr->getText() === $key->getText();
        })
        ->count() > 0;
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
  public function getKeys($recursive = TRUE, $echo = FALSE) {
    $keys = new NodeCollection();
    foreach ($this->elements->getItems() as $index => $element) {
      if ($element instanceof ArrayPairNode) {
        $keys->add($element->getKey());
        $value = $element->getValue();
      }
      else {
        $keys->add(IntegerNode::fromValue($index), FALSE);
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
    foreach ($elements as $element) {
      $node->getElementList()->appendItem($element);
    }
    return $node;
  }
}
