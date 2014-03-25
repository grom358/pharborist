<?php
namespace Pharborist;

/**
 * A static variable declaration.
 *
 * For example, $a = A_SCALAR_VALUE
 */
class StaticVariableNode extends ParentNode {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $initialValue;
}
