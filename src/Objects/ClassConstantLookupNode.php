<?php
namespace Pharborist\Objects;

use Pharborist\ParentNode;
use Pharborist\Node;
use Pharborist\ExpressionNode;
use Pharborist\TokenNode;

/**
 * A class constant lookup, e.g. `MyClass::MY_CONST`
 */
class ClassConstantLookupNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  protected $className;

  /**
   * @var TokenNode
   */
  protected $constantName;

  /**
   * @return Node
   */
  public function getClassName() {
    return $this->className;
  }

  /**
   * @return TokenNode
   */
  public function getConstantName() {
    return $this->constantName;
  }
}
