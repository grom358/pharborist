<?php
namespace Pharborist;

/**
 * Constant declaration list.
 * @package Pharborist
 */
class ConstantDeclarationListNode extends CollectionNode {
  /**
   * @var ConstantDeclarationNode[]
   */
  public $declarations;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->declarations = &$this->items;
  }
}
