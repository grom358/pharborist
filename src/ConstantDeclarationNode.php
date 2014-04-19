<?php
namespace Pharborist;

/**
 * Constant declaration.
 */
class ConstantDeclarationNode extends ParentNode {
  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var ExpressionNode
   */
  protected $value;

  /**
   * @return TokenNode
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
