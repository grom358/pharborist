<?php
namespace Pharborist;

/**
 * A function/method call.
 */
abstract class CallNode extends ParentNode {
  /**
   * @var ArgumentListNode
   */
  protected $arguments;

  /**
   * @return ExpressionNode[]
   */
  public function getArguments() {
    return $this->arguments->getArguments();
  }
}
