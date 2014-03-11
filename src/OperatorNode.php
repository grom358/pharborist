<?php
namespace Pharborist;

/**
 * An operator in an expression.
 * @package Pharborist
 */
class OperatorNode extends Node {
  const MODE_UNARY = 1;
  const MODE_BINARY = 2;

  const ASSOC_LEFT = 1;
  const ASSOC_RIGHT = 2;
  const ASSOC_NONE = 3;

  /**
   * @var int
   */
  public $mode;

  /**
   * @var int
   */
  public $precedence;

  /**
   * @var int
   */
  public $associativity;

  /**
   * @var int
   */
  public $type;

  /**
   * @var bool
   */
  public $hasBinaryMode;

  /**
   * @var bool
   */
  public $hasUnaryMode;
}
