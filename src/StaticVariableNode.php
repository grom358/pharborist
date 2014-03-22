<?php
namespace Pharborist;

/**
 * A static variable declaration. Eg. $a = A_SCALAR_VALUE
 * @package Pharborist
 */
class StaticVariableNode extends Node {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $initialValue;
}
