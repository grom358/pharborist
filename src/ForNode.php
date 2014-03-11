<?php
namespace Pharborist;

/**
 * for control structure
 * @package Pharborist
 */
class ForNode extends Node {
  /**
   * @var Node
   */
  public $initial;

  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $step;

  /**
   * @var Node
   */
  public $body;
}
