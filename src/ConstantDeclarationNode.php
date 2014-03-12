<?php
namespace Pharborist;

/**
 * Constant declaration.
 * @package Pharborist
 */
class ConstantDeclarationNode extends Node {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $value;
}
