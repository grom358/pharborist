<?php
namespace Pharborist;

/**
 * A reference variable.
 *
 * For example, &$a
 */
class ReferenceVariableNode extends ParentNode {
  /**
   * @var Node
   */
  public $variable;
}
