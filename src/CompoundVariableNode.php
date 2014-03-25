<?php
namespace Pharborist;

/**
 * A compound variable.
 *
 * For example, ${expr()}
 */
class CompoundVariableNode extends ParentNode {
  /**
   * @var Node
   */
  public $expression;
}
