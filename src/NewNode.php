<?php
namespace Pharborist;

/**
 * A new expression.
 *
 * For example, new MyClass()
 */
class NewNode extends ParentNode implements ExpressionNode {
  /**
   * @var NameNode
   */
  protected $className;

  /**
   * @var ArgumentListNode
   */
  protected $arguments;

  /**
   * @return NameNode
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
