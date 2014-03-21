<?php
namespace Pharborist;

/**
 * A list of declare directives.
 * @package Pharborist
 */
class DeclareDirectiveListNode extends CollectionNode {
  /**
   * @var DeclareDirectiveNode[]
   */
  public $directives;

  public function __construct() {
    $this->directives = &$this->items;
  }
}
