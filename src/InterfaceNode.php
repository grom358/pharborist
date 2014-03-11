<?php
namespace Pharborist;

/**
 * An interface declaration.
 * @package Pharborist
 */
class InterfaceNode extends Node {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node[]
   */
  public $extends = array();

  /**
   * @var Node[]
   */
  public $constants = array();

  /**
   * @var Node[]
   */
  public $methods = array();
}
