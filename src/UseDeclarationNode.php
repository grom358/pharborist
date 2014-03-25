<?php
namespace Pharborist;

/**
 * Use declaration.
 */
class UseDeclarationNode extends ParentNode {
  /**
   * @var Node
   */
  public $namespacePath;

  /**
   * @var Node
   */
  public $alias;
}
