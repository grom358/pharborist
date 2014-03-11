<?php
namespace Pharborist;

/**
 * elseif control structure.
 * @package Pharborist
 */
class ElseIfNode extends Node {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $then;
}
