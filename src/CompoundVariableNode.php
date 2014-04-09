<?php
namespace Pharborist;

/**
 * A compound variable.
 *
 * For example, ${expr()}
 */
class CompoundVariableNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'expression' => NULL,
  );

  /**
   * @return ExpressionNode
   */
  public function getExpression() {
    return $this->properties['expression'];
  }
}
