<?php
namespace Pharborist;

/**
 * A require_once.
 */
class RequireOnceNode extends ParentNode implements ExpressionNode {
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
