<?php
namespace Pharborist;

/**
 * An include(_once) or require(_once) expression.
 */
abstract class ImportNode extends ParentNode implements ExpressionNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var ExpressionNode
   */
  protected $expression;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @var ExpressionNode
   */
  public function getExpression() {
    return $this->expression;
  }
}
