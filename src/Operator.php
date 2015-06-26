<?php
namespace Pharborist;

/**
 * An operator in an expression; Used by the parser to hold data about
 * an operator.
 *
 * @internal
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
  protected $operator;

  /**
   * @return TokenNode
   */
  public function getOperator() {
    return $this->operator;
  }

  /**
   * Colon node. Only used by ternary operator.
   * @var PartialNode
   */
  public $colon;

  /**
   * Then node. Only used by ternary operator.
   */
  public $then;

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
   * Node class to create if unary operator.
   * @var string
   */
  public $unaryClassName;

  /**
   * Node class to create if binary operator.
   * @var string
   */
  public $binaryClassName;
}
