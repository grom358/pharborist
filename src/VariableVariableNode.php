<?php
namespace Pharborist;

/**
 * A variable variable.
 *
 * For example, $$a
 */
class VariableVariableNode extends ParentNode {
  /**
   * @var Node
   */
  public $variable;
}
