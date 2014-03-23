<?php
namespace Pharborist;

/**
 * A ternary operator. Eg. $condition ? $then : $else
 * @package Pharborist
 */
class TernaryOperatorNode extends Node {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $then;

  /**
   * @var Node
   */
  public $else;
}
