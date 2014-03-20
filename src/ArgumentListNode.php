<?php
namespace Pharborist;

/**
 * List of arguments.
 * @package Pharborist
 */
class ArgumentListNode extends CollectionNode {
  /**
   * @var Node[]
   */
  public $arguments;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->arguments = &$this->items;
  }
}
