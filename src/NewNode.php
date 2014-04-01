<?php
namespace Pharborist;

/**
 * A new expression.
 *
 * For example, new MyClass()
 */
class NewNode extends ParentNode implements ExpressionNode {
  /**
   * @var NamespacePathNode
   */
  public $className;

  /**
   * @var Node[]
   */
  public $arguments = array();
}
