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
   * @return NodeCollection
   */
  public function getKeys($recursive = TRUE) {
    $keys = new NodeCollection([]);
    foreach ($this->elements->getItems() as $element) {
      if ($element instanceof ArrayPairNode) {
        $keys->add($element->getKey());

        $value = $element->getValue();
        if ($value instanceof ArrayNode && $recursive) {
          $keys->add($value->getKeys($recursive));
        }
      }
    }
    return $keys;
  }

  /**
   * @return NodeCollection
   */
  public function getValues($flatten = TRUE) {
    $values = new NodeCollection([]);
    foreach ($this->elements->getItems() as $element) {
      if ($element instanceof ArrayPairNode) {
        $value = $element->getValue();
        if ($value instanceof ArrayNode && $flatten) {
          $values->add($value->getValues($flatten));
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
