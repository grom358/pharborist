<?php
namespace Pharborist;

/**
 * An operator in an expression. Used only by the parser internally.
 * @package Pharborist
 */
class Operator extends PartialNode {
  const MODE_UNARY = 1;
  const MODE_BINARY = 2;

  const ASSOC_LEFT = 1;
  const ASSOC_RIGHT = 2;
  const ASSOC_NONE = 3;

  /**
   * @var TokenNode
   */
  public $operatorNode;

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

  /**
   * Get the position of the operator.
   */
  public function getSourcePosition() {
    return $this->operatorNode->getSourcePosition();
  }
}
