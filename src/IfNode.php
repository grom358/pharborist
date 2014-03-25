<?php
namespace Pharborist;

/**
 * An if control structure.
 */
class IfNode extends StatementNode {
  /**
   * @var Node
   */
  public $condition;

  /**
   * @var Node
   */
  public $then;

  /**
   * @var Node[]
   */
  public $elseIfList = array();

  /**
   * @var Node
   */
  public $else;
}
