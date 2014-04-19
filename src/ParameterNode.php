<?php
namespace Pharborist;

/**
 * A function parameter.
 */
class ParameterNode extends ParentNode {
  /**
   * @var Node
   */
  protected $classType;

  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var ExpressionNode
   */
  protected $value;

  /**
   * @return Node
   */
  public function getClassType() {
    return $this->classType;
  }

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * @return Node
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return ExpressionNode
   */
  public function getValue() {
    return $this->value;
  }
}
