<?php
namespace Pharborist;

/**
 * Ellipsis parameter.
 *
 * For example, a_func($a, ...$b);
 */
class EllipsisNode extends ParentNode {
  protected $properties = array(
    'expression' => NULL,
  );

  /**
   * @return Node
   */
  public function getExpression() {
    return $this->properties['expression'];
  }
}
