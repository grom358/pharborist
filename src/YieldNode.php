<?php
namespace Pharborist;

/**
 * A yield expression.
 */
class YieldNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $key;

  /**
   * @var Node
   */
  public $value;
}
