<?php
namespace Pharborist;

/**
 * An if control structure.
 */
class IfNode extends StatementNode {
  protected $properties = array(
    'condition' => NULL,
    'then' => NULL,
    'else' => NULL,
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

  /**
   * @return ElseIfNode[]
   */
  public function getElseIfList() {
    return $this->childrenByInstance('\Pharborist\ElseIfNode');
  }

  /**
   * @return Node
   */
  public function getElse() {
    return $this->properties['else'];
  }
}
