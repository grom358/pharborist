<?php
namespace Pharborist;

/**
 * while control structure.
 * @package Pharborist
 */
class WhileNode extends Node {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $body;
}
