<?php
namespace Pharborist;

/**
 * A ternary operation.
 *
 * For example, $condition ? $then : $else
 */
class TernaryOperationNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'condition' => NULL,
    'then' => NULL,
    'else' => NULL,
  );

  /**
   * @return Node
   */
  public function getCondition() {
    return $this->properties['condition'];
  }

  /**
   * @return Node
   */
  public function getThen() {
    return $this->properties['then'];
  }

  /**
   * @return Node
   */
  public function getElse() {
    return $this->properties['else'];
  }
}
