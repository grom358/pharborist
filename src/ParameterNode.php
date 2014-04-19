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
  protected $defaultValue;

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
   * @return Node
   */
  public function getDefaultValue() {
    return $this->defaultValue;
  }
}
