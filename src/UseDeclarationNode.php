<?php
namespace Pharborist;

/**
 * Use declaration.
 * @package Pharborist
 */
class UseDeclarationNode extends Node {
  /**
   * @var Node
   */
  public $namespacePath;

  /**
   * @var Node
   */
  public $alias;
}
