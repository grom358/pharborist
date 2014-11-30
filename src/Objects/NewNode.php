<?php
namespace Pharborist\Objects;

use Pharborist\ExpressionNode;
use Pharborist\Functions\ArgumentTrait;
use Pharborist\Namespaces\NameNode;
use Pharborist\ParentNode;
use Pharborist\ParenTrait;

/**
 * A new expression, e.g. `new Foo()`
 *
 * You can access and modify the constructor arguments as with any other
 * function or method call.
 */
class NewNode extends ParentNode implements ExpressionNode {
  use ArgumentTrait;
  use ParenTrait;

  /**
   * @var NameNode
   */
  protected $className;

  /**
   * Returns the name of the instantiated class.
   *
   * @return NameNode
   */
  public function getClassName() {
    return $this->className;
  }
}
