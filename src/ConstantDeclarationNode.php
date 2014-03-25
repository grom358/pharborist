<?php
namespace Pharborist;

/**
 * Constant declaration.
 */
class ConstantDeclarationNode extends ParentNode {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $value;
}
