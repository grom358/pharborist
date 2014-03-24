<?php
namespace Pharborist;

/**
 * A binary operation.
 * @package Pharborist
 */
abstract class BinaryOperationNode extends Node {
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
