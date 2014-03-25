<?php
namespace Pharborist;

/**
 * elseif control structure.
 */
class ElseIfNode extends ParentNode {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $then;
}
