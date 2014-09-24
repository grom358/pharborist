<?php

/**
 * @file
 * Contains \Pharborist\Constants\ConstantDeclarationNode.
 */

namespace Pharborist\Constants;

use Pharborist\ParentNode;
use Pharborist\NameNode;
use Pharborist\ExpressionNode;
use Pharborist\TokenNode;

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
   * @return NameNode
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
