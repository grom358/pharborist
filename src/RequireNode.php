<?php
namespace Pharborist;

/**
 * A require.
 */
class RequireNode extends ParentNode implements ExpressionNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var Node
   */
  public $expression;
}
