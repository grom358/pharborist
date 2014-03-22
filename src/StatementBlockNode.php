<?php
namespace Pharborist;

/**
 * A block of statements.
 * @package Pharborist
 */
class StatementBlockNode extends Node {
  /**
   * @var StatementNode[]
   */
  public $statements = array();
}
