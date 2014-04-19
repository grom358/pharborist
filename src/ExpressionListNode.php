<?php
namespace Pharborist;

class ExpressionListNode extends ParentNode {
  /**
   * @return ExpressionNode[]
   */
  public function getExpressions() {
    return $this->childrenByInstance('\Pharborist\ExpressionNode');
  }
}
