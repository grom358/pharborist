<?php
namespace Pharborist;

/**
 * A try control structure.
 */
class TryNode extends StatementNode {
  /**
   * @var Node
   */
  public $try;

  /**
   * @var Node[]
   */
  public $catches = array();

  /**
   * @var Node
   */
  public $finally;
}
