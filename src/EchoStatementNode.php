<?php
namespace Pharborist;

/**
 * An echo statement.
 */
class EchoStatementNode extends StatementNode {
  /**
   * Returns the expressions being echoed.
   *
   * @return ExpressionNode[]
   */
  public function getExpressions() {
    return $this->childrenByInstance('\Pharborist\ExpressionNode');
  }
}
