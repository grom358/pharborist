<?php
namespace Pharborist;

/**
 * A case statement in switch control structure.
 */
class CaseNode extends ParentNode {
  /**
   * @var Node
   */
  public $matchOn;

  /**
   * @var Node
   */
  public $body;
}
