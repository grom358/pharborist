<?php
namespace Pharborist;

/**
 * A template echo tag statement.
 *
 * For example, <?=$a?>
 */
class EchoTagStatementNode extends StatementNode {
  /**
   * @return ExpressionNode[]
   */
  public function getExpressions() {
    return $this->childrenByInstance('\Pharborist\ExpressionNode');
  }
}
