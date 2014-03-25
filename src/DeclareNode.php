<?php
namespace Pharborist;

/**
 * A declare control structure.
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
