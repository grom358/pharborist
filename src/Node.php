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
   * @var Node
   */
  protected $previous = NULL;

  /**
   * @var Node
   */
  protected $next = NULL;

  /**
   * Get the parent node.
   * @return ParentNode
   */
  public function getParent() {
    return $this->parent;
  }

  /**
   * Get the ancestors of this node.
   * @return array
   */
  public function getAncestors() {
    $ancestors = array();
    $ancestor = $this->parent;
    while ($ancestor !== NULL) {
      $ancestors[] = $ancestor;
    }
    return $ancestors;
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

  /**
   * Insert a node before this node.
   * @param Node $node
   * @return $this
   */
  public function insertBefore(Node $node) {
    $this->parent->insertBeforeChild($this, $node);
    return $this;
  }

  /**
   * Insert a node after this node.
   * @param Node $node
   * @return $this
   */
  public function insertAfter(Node $node) {
    $this->parent->insertAfterChild($this, $node);
    return $this;
  }

  /**
   * Remove node from its parent.
   * @return $this
   */
  public function remove() {
    $this->parent->removeChild($this);
    return $this;
  }

  /**
   * Replace this node in its parent.
   * @param Node $node
   * @return $this
   */
  public function replace(Node $node) {
    $this->parent->replaceChild($this, $node);
    return $this;
  }

  /**
   * Get the previous sibling.
   * @return Node
   */
  public function previousSibling() {
    return $this->previous;
  }

  /**
   * Get the next sibling.
   * @return Node
   */
  public function nextSibling() {
    return $this->next;
  }

  abstract public function getSourcePosition();
}
