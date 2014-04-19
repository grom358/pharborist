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
   * @return ArrayElementNode[]
   */
  public function getElements() {
    return $this->elements->getItems();
  }
}
