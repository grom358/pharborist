<?php
namespace Pharborist;

/**
 * A constant lookup.
 *
 * For example,
 * MyNamespace\MY_CONST
 */
class ConstantNode extends ParentNode implements ExpressionNode {
  /**
   * @var NameNode
   */
  protected $constantName;

  /**
   * @return NameNode
   */
  public function getConstantName() {
    return $this->constantName;
  }
}
