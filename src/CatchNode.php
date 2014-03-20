<?php
namespace Pharborist;

/**
 * A catch in a try control structure.
 * @package Pharborist
 */
class CatchNode extends Node {
  /**
   * @var NamespacePathNode
   */
  public $exceptionType;

  /**
   * @var VariableNode
   */
  public $variable;

  /**
   * @var Node
   */
  public $body;
}
