<?php
namespace Pharborist;

/**
 * while control structure.
 * @package Pharborist
 */
class WhileNode extends StatementNode {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $body;
}
