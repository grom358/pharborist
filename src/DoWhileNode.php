<?php
namespace Pharborist;

/**
 * do while control structure.
 * @package Pharborist
 */
class DoWhileNode extends StatementNode {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $body;
}
