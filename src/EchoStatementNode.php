<?php
namespace Pharborist;

/**
 * An echo statement.
 */
class EchoStatementNode extends StatementNode {
  /**
   * @return ExpressionNode[]
   */
  public function getExpressions() {
    return $this->childrenByInstance('\Pharborist\ExpressionNode');
  }
}
