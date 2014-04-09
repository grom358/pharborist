<?php
namespace Pharborist;

/**
 * An include_once.
 */
class IncludeOnceNode extends ParentNode implements ExpressionNode {
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
