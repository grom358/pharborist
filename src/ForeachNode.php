<?php
namespace Pharborist;

/**
 * foreach control structure.
 */
class ForeachNode extends StatementNode {
  /**
   * @var Node
   */
  public $onEach;

  /**
   * @var Node
   */
  public $key;

  /**
   * @var Node
   */
  public $value;

  /**
   * @var Node
   */
  public $body;
}
