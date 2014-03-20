<?php
namespace Pharborist;

/**
 * Node for php array.
 * @package Pharborist
 */
class ArrayNode extends CollectionNode {
  /**
   * @var Node[]
   */
  public $elements;

  public function __construct() {
    $this->elements = &$this->items;
  }
}
