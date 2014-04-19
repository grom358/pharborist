<?php
namespace Pharborist;

/**
 * A parenthesis expression.
 */
class ParenthesisNode extends ParentNode implements ExpressionNode {
  protected $properties = ['expression' => NULL];

  /**
   * @var ExpressionNode
   */
  public function getExpression() {
    return $this->properties['expression'];
  }
}
