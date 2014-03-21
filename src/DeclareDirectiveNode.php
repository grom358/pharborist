<?php
namespace Pharborist;

/**
 * A declare directive.
 * @package Pharborist
 */
class DeclareDirectiveNode extends Node {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $value;
}
