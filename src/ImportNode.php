<?php
namespace Pharborist;

/**
 * An include(_once) or require(_once) expression.
 */
abstract class ImportNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'docComment' => NULL,
    'expression' => NULL,
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }

  /**
   * @var ExpressionNode
   */
  public function getExpression() {
    return $this->properties['expression'];
  }
}
