<?php
namespace Pharborist;

/**
 * A declare control structure.
 * @package Pharborist
 */
class DeclareNode extends StatementNode {
  /**
   * @var DeclareDirectiveNode[]
   */
  public $directives = array();

  /**
   * @var Node
   */
  public $body;
}
