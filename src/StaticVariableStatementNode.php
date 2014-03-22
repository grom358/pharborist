<?php
namespace Pharborist;

/**
 * A static variable statement. Eg. static $a, $b = A_SCALAR;
 * @package Pharborist
 */
class StaticVariableStatementNode extends Node {
  /**
   * @var StaticVariableNode[]
   */
  public $variables = array();
}
