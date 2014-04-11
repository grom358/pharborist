<?php
namespace Pharborist;

/**
 * An expression statement.
 *
 * For example, expr();
 */
class ExpressionStatementNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }
}
