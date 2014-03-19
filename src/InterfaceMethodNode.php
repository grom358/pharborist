<?php
namespace Pharborist;

/**
 * Interface method.
 * @package Pharborist
 */
class InterfaceMethodNode extends Node {
  /**
   * @var Node
   */
  public $visibility;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var ParameterListNode
   */
  public $parameters;
}
