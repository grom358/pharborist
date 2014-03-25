<?php
namespace Pharborist;

/**
 * An unary operation.
 */
abstract class UnaryOperationNode extends ParentNode {
  /**
   * @var Node
   */
  public $operator;

  /**
   * @var Node
   */
  public $operand;
}
