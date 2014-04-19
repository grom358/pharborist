<?php
namespace Pharborist;

/**
 * Node for php array.
 */
class ArrayNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'elements' => NULL,
  );

  /**
   * @return Node[]
   */
  public function getElements() {
    /** @var CommaListNode $elements */
    $elements = $this->properties['elements'];
    return $elements->getItems();
  }
}
