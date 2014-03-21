<?php
namespace Pharborist;

/**
 * A declare control structure.
 * @package Pharborist
 */
class DeclareNode extends Node {
  /**
   * @var DeclareDirectiveListNode
   */
  public $directives;

  /**
   * @var Node
   */
  public $body;
}
