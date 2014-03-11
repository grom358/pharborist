<?php
namespace Pharborist;

/**
 * Class declaration.
 * @package Pharborist
 */
class ClassNode extends Node {
  /**
   * @var Node
   */
  public $abstract;

  /**
   * @var Node
   */
  public $final;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $extends;

  /**
   * @var Node[]
   */
  public $implements = array();

  /**
   * @var Node[]
   */
  public $statements = array();
}
