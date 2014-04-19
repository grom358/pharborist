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
   * @var NamespacePathNode
   */
  protected $constantName;

  /**
   * @return NamespacePathNode
   */
  public function getConstant() {
    return $this->constantName;
  }
}
