<?php
namespace Pharborist;

/**
 * A node in the PHP syntax tree.
 */
abstract class Node {
  /**
   * @var ParentNode
   */
  protected $parent = NULL;

  /**
   * Get the parent node.
   * @return ParentNode
   */
  public function getParent() {
    return $this->parent;
  }

  abstract public function getSourcePosition();
}
