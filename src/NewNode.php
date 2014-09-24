<?php
namespace Pharborist;

/**
 * A new object being created, e.g. `$foo = new Foo();`. You can access the constructor
 * arguments as with any function or method call.
 */
class NewNode extends ParentNode implements ExpressionNode {
  use ArgumentTrait;

  /**
   * @var NameNode
   */
  protected $className;

  /**
   * @return NameNode
   */
  public function getClassName() {
    return $this->className;
  }
}
