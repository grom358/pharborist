<?php
namespace Pharborist;

/**
 * An include_once.
 */
class IncludeOnceNode extends ParentNode implements ExpressionNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var ExpressionNode
   */
  public $expression;
}
