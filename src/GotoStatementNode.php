<?php
namespace Pharborist;

/**
 * A goto statement.
 * @package Pharborist
 */
class GotoStatementNode extends Node {
  /**
   * @var Node
   */
  public $label;
}
