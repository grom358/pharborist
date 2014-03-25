<?php
namespace Pharborist;

/**
 * while control structure.
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
