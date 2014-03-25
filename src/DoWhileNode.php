<?php
namespace Pharborist;

/**
 * do while control structure.
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
