<?php
namespace Pharborist;

/**
 * A declare control structure.
 * @package Pharborist
 */
class DeclareNode extends StatementNode {
  /**
   * @var DeclareDirectiveListNode
   */
  public $directives;

  /**
   * @var Node
   */
  public $body;
}
