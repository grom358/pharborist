<?php
namespace Pharborist;

/**
 * Node for php array.
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
   * @return array
   */
  public function getValue() {
    $ret = array();
    foreach ($this->elements->getItems() as $element) {
      if ($element instanceof ArrayPairNode) {
        $key = $element->getKey()->getValue();
        $value = $element->getValue()->getValue();
        $ret[$key] = $value;
      }
      else {
        $ret[] = $element->getValue();
      }
    }
    return $ret;
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
