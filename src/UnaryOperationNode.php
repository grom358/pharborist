<?php
namespace Pharborist;

/**
 * An unary operation.
 * @package Pharborist
 */
abstract class UnaryOperationNode extends Node {
  /**
   * @var Node
   */
  public $operator;

  /**
   * @var Node
   */
  public $operand;
}
