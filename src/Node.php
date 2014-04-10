<?php
namespace Pharborist;

/**
 * A node in the PHP syntax tree.
 */
abstract class Node implements NodeInterface {
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

  public function getParent() {
    return $this->parent;
  }

  public function getAncestors() {
    $ancestors = array();
    $ancestor = $this->parent;
    while ($ancestor !== NULL) {
      $ancestors[] = $ancestor;
    }
    return $ancestors;
  }

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

  public function insertBefore(Node $node) {
    $this->parent->insertBeforeChild($this, $node);
    return $this;
  }

  public function insertAfter(Node $node) {
    $this->parent->insertAfterChild($this, $node);
    return $this;
  }

  public function remove() {
    $this->parent->removeChild($this);
    return $this;
  }

  public function replace(Node $node) {
    $this->parent->replaceChild($this, $node);
    return $this;
  }

  public function previousSibling() {
    return $this->previous;
  }

  public function nextSibling() {
    return $this->next;
  }

  abstract public function getSourcePosition();
}
