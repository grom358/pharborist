<?php
namespace Pharborist;

/**
 * A case statement in switch control structure.
 * @package Pharborist
 */
class CaseNode extends Node {
  /**
   * @var Node
   */
  public $matchOn;

  /**
   * @var Node
   */
  public $body;
}
