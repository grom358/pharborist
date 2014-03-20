<?php
namespace Pharborist;

/**
 * A list of trait names.
 * @package Pharborist
 */
class TraitNameListNode extends CollectionNode {
  /**
   * @var Node[]
   */
  public $names;

  public function __construct() {
    $this->names = &$this->items;
  }
}
