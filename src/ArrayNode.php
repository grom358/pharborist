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
}
