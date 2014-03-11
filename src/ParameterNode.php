<?php
namespace Pharborist;

/**
 * A function parameter.
 * @package Pharborist
 */
class ParameterNode extends Node {
  /**
   * @var Node
   */
  public $classType;

  /**
   * @var Node
   */
  public $reference;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $defaultValue;
}
