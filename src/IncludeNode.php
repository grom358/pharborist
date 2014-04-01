<?php
namespace Pharborist;

/**
 * An include.
 */
class IncludeNode extends ParentNode implements ExpressionNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var ExpressionNode
   */
  public $expression;
}
