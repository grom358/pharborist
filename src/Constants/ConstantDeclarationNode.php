<?php
namespace Pharborist\Constants;

use Pharborist\Namespaces\IdentifierNameTrait;
use Pharborist\ParentNode;
use Pharborist\ExpressionNode;

/**
 * Constant declaration.
 */
class ConstantDeclarationNode extends ParentNode {
  use IdentifierNameTrait;

  /**
   * @var ExpressionNode
   */
  protected $value;

  /**
   * @return ExpressionNode
   */
  public function getValue() {
    return $this->value;
  }
}
