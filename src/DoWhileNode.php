<?php
namespace Pharborist;

/**
 * do while control structure.
 * @package Pharborist
 */
class DoWhileNode extends Node {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $body;
}
