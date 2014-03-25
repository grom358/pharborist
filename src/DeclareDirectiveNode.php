<?php
namespace Pharborist;

/**
 * A declare directive.
 */
class DeclareDirectiveNode extends ParentNode {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $value;
}
