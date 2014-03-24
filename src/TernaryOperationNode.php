<?php
namespace Pharborist;

/**
 * A ternary operation. Eg. $condition ? $then : $else
 * @package Pharborist
 */
class TernaryOperationNode extends Node {
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
