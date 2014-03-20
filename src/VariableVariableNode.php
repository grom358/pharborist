<?php
namespace Pharborist;

/**
 * A variable variable. Eg. $$a
 * @package Pharborist
 */
class VariableVariableNode extends Node {
  /**
   * @var Node
   */
  public $variable;
}
