<?php
namespace Pharborist;

/**
 * A for control structure.
 */
class ForNode extends StatementNode {
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
