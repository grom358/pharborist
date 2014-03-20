<?php
namespace Pharborist;

/**
 * A compound variable. eg ${expr()}
 * @package Pharborist
 */
class CompoundVariableNode extends Node {
  /**
   * @var Node
   */
  public $expression;
}
