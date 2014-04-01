<?php
namespace Pharborist;

/**
 * Node for php array.
 */
class ArrayNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node[]
   */
  public $elements = array();
}
