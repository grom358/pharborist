<?php
namespace Pharborist;


class ConstantDeclarationListNode extends ListNode {
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
