<?php
namespace Pharborist;

/**
 * A binary operator.
 * @package Pharborist
 */
class BinaryOperatorNode extends Node {
  /**
   * @var Node
   */
  public $left;

  /**
   * @var Node
   */
  public $operator;

  /**
   * @var Node
   */
  public $right;
}
