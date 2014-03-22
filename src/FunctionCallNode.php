<?php
namespace Pharborist;

/**
 * A function call.
 * @package Pharborist
 */
class FunctionCallNode extends Node {
  /**
   * @var Node
   */
  public $functionReference;

  /**
   * @var Node[]
   */
  public $arguments = array();
}
