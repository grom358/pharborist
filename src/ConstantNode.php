<?php
namespace Pharborist;

/**
 * A constant lookup.
 *
 * For example,
 * MyNamespace\MY_CONST
 */
class ConstantNode extends ParentNode implements ExpressionNode {
  protected $properties = ['constant' => NULL];

  /**
   * @return NamespacePathNode
   */
  public function getConstant() {
    return $this->properties['constant'];
  }
}
