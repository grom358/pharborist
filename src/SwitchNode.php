<?php
namespace Pharborist;

/**
 * A switch control structure.
 */
class SwitchNode extends StatementNode {
  /**
   * @var Node
   */
  public $switchOn;

  /**
   * @var CaseNode[]
   */
  public $cases = array();
}
