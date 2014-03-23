<?php
namespace Pharborist;

/**
 * An unary operator.
 * @package Pharborist
 */
class UnaryOperatorNode extends Node {
  /**
   * @var Node
   */
  public $operator;

  /**
   * @var Node
   */
  public $operand;
}
