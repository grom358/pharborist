<?php
namespace Pharborist;

/**
 * Interface method.
 * @package Pharborist
 */
class InterfaceMethodNode extends Node {
  /**
   * @var Token
   */
  public $visibility;

  /**
   * @var Token
   */
  public $methodName;

  /**
   * @var Node
   */
  public $parameters;
}
