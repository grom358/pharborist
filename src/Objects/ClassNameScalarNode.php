<?php
namespace Pharborist\Objects;

use Pharborist\ParentNode;
use Pharborist\Node;
use Pharborist\ExpressionNode;

/**
 * A class name scalar (in PHP 5.5 and later). Example: `MyClass::class`.
 */
class ClassNameScalarNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  protected $className;

  /**
   * @return Node
   */
  public function getClassName() {
    return $this->className;
  }
}
