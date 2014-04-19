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
   * @return (ArrayPairNode|ExpressionNode)[]
   */
  public function getElements() {
    return $this->elements->getItems();
  }
}
