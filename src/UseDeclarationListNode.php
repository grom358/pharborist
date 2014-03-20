<?php
namespace Pharborist;

/**
 * Use declaration list.
 * @package Pharborist
 */
class UseDeclarationListNode extends CollectionNode {
  /**
   * @var UseDeclarationNode[]
   */
  public $declarations;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->declarations = &$this->items;
    $this->collectionType = '\Pharborist\UseDeclarationNode';
  }
}
