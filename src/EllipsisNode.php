<?php
namespace Pharborist;

/**
 * Ellipsis parameter.
 *
 * For example, a_func($a, ...$b);
 */
class EllipsisNode extends ParentNode {
  /**
   * @var Node
   */
  public $expression;
}
