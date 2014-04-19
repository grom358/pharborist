<?php
namespace Pharborist;

/**
 * List of function/method call arguments.
 */
class ArgumentListNode extends ParentNode {
  public function getArguments() {
    return $this->childrenByInstance('\Pharborist\ExpressionNode');
  }
}
