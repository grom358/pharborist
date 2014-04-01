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

  /**
   * Get the ancestor of given type.
   * @param string $type
   * @return ParentNode
   */
  public function getAncestor($type) {
    $ancestor = $this->parent;
    while ($ancestor !== NULL) {
      if ($ancestor instanceof $type) {
        return $ancestor;
      }
      $ancestor = $ancestor->parent;
    }
    return NULL;
  }

  abstract public function getSourcePosition();
}
