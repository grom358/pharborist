<?php
namespace Pharborist;

/**
 * A reference variable. Eg. &$a
 * @package Pharborist
 */
class ReferenceVariableNode extends Node {
  /**
   * @var Node
   */
  public $variable;
}
