<?php
namespace Pharborist;

/**
 * A class member list declaration.
 * @package Pharborist
 */
class ClassMemberListNode extends CollectionNode {
  /**
   * @var ModifiersNode
   */
  public $modifiers;

  /**
   * @var ClassMemberNode[]
   */
  public $members;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->members = &$this->items;
  }
}
