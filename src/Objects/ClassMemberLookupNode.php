<?php
namespace Pharborist\Objects;

use Pharborist\Node;
use Pharborist\ParentNode;
use Pharborist\Variables\VariableExpressionNode;

/**
 * A class member lookup.
 *
 * For example, MyClass::$a
 */
class ClassMemberLookupNode extends ParentNode implements VariableExpressionNode {
  /**
   * @var Node
   */
  protected $className;

  /**
   * @var Node
   */
  protected $memberName;

  /**
   * @return Node
   */
  public function getClassName() {
    return $this->className;
  }

  /**
   * @return Node
   */
  public function getMemberName() {
    return $this->memberName;
  }
}
