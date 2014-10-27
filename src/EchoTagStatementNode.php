<?php
namespace Pharborist;

/**
 * A template echo tag statement.
 *
 * For example, <?=$a?>
 */
class EchoTagStatementNode extends StatementNode {
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
   * @return NodeCollection|ExpressionNode[]
   */
  public function getExpressions() {
    return $this->expressions->getItems();
  }
}
