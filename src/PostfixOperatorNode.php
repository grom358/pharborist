<?php
namespace Pharborist;

abstract class PostfixOperatorNode extends Node {
  /**
   * @var Node
   */
  public $operand;

  /**
   * @var Node
   */
  public $operator;
}
