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
  protected $className;

  /**
   * @var ArgumentListNode
   */
  protected $arguments;

  /**
   * @return NamespacePathNode
   */
  public function getClassName() {
    return $this->className;
  }

  /**
   * @return ExpressionNode[]
   */
  public function getArguments() {
    return $this->arguments->getArguments();
  }
}
