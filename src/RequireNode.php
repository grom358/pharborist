<?php
namespace Pharborist;

/**
 * A require.
 */
class RequireNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'expression' => NULL,
  );

  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var ExpressionNode
   */
  public function getExpression() {
    return $this->properties['expression'];
  }
}
