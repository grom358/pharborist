<?php
namespace Pharborist;

/**
 * A function/method call.
 */
abstract class CallNode extends ParentNode {
  /**
   * @return ExpressionNode[]
   */
  abstract public function getArguments();
}
