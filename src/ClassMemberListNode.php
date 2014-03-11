<?php
namespace Pharborist;

/**
 * A class member list declaration.
 * @package Pharborist
 */
class ClassMemberListNode extends Node {
  /**
   * @var ModifiersNode
   */
  public $modifiers;

  /**
   * @var Node
   */
  public $members = [];
}
