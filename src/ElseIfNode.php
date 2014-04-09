<?php
namespace Pharborist;

/**
 * elseif control structure.
 */
class ElseIfNode extends ParentNode {
  protected $properties = array(
    'condition' => NULL,
    'then' => NULL,
  );

  /**
   * @return ExpressionNode
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
}
