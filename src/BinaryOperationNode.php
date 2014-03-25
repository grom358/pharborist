<?php
namespace Pharborist;

/**
 * A binary operation.
 */
abstract class BinaryOperationNode extends ParentNode {
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
