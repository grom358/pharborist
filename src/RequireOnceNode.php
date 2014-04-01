<?php
namespace Pharborist;

/**
 * A require_once.
 */
class RequireOnceNode extends ParentNode implements ExpressionNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var Node
   */
  public $expression;
}
