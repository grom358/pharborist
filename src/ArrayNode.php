<?php
namespace Pharborist;

/**
 * Node for php array.
 */
class ArrayNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'elements' => array(),
  );

  /**
   * @return Node[]
   */
  public function getElements() {
    return $this->properties['elements'];
  }
}
