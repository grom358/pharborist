<?php
namespace Pharborist;

/**
 * An echo statement.
 */
class EchoStatementNode extends StatementNode {
  /**
   * @var CommaListNode
   */
  protected $expressions;

  /**
   * @return CommaListNode
   */
  public function getExpressionList() {
    return $this->expressions;
  }

  /**
   * Return the expressions being echoed.
   *
   * @return NodeCollection|ExpressionNode[]
   */
  public function getExpressions() {
    return $this->expressions->getItems();
  }
}
