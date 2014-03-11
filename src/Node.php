<?php
namespace Pharborist;

/**
 * A node in the PHP syntax tree.
 * @package Pharborist
 */
class Node {
  /**
   * @var Node
   */
  public $parent = NULL;

  /**
   * @var Node[]
   */
  public $children = array();

  /**
   * @var int|string
   */
  public $type;

  /**
   * Prepend a child to node.
   */
  public function prependChild(Node $child) {
    $child->parent = $this;
    array_unshift($this->children, $child);
    return $child;
  }

  /**
   * Append a child to node.
   * @param Node $child
   * @return Node
   */
  public function appendChild(Node $child) {
    $child->parent = $this;
    $this->children[] = $child;
    return $child;
  }

  /**
   * Append children to node
   * @param Node[] $children
   */
  public function appendChildren(array $children) {
    foreach ($children as $child) {
      $this->appendChild($child);
    }
  }

  /**
   * @return SourcePosition
   */
  public function getSourcePosition() {
    if (count($this->children) === 0) {
      return $this->parent->getSourcePosition();
    }
    $child = $this->children[0];
    return $child->getSourcePosition();
  }

  /**
   * @return string
   */
  public function __toString() {
    $str = '';
    foreach ($this->children as $child) {
      $str .= (string) $child;
    }
    return $str;
  }
}
