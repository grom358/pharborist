<?php
namespace Pharborist;

/**
 * Use declaration list.
 * @package Pharborist
 */
class UseDeclarationListNode extends ListNode {
  /**
   * @var Node[]
   */
  public $declarations;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->declarations = &$this->items;
  }
}
