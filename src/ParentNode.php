<?php
namespace Pharborist;

/**
 * A node that has children.
 */
abstract class ParentNode extends Node {
  /**
   * @var Node[]
   */
  protected $children = array();

  /**
   * Get child at index.
   * @param int $index
   * @return Node
   */
  public function getChild($index) {
    return $this->children[$index];
  }

  /**
   * Get the number of children.
   * @return int
   */
  public function getChildCount() {
    return count($this->children);
  }

  /**
   * @param Node $child
   * @return int
   * @throws \Exception
   */
  public function indexOf(Node $child) {
    foreach ($this->children as $i => $element) {
      if ($element === $child) {
        return $i;
      }
    }
    throw new \Exception('Child node not found!');
  }

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
   * Append children to node.
   * @param Node[] $children
   */
  public function appendChildren(array $children) {
    foreach ($children as $child) {
      $this->appendChild($child);
    }
  }

  /**
   * Merge another parent node into this node.
   * @param ParentNode $node
   */
  public function mergeNode(ParentNode $node) {
    $this->appendChildren($node->children);
  }

  /**
   * Remove the first child.
   * @return Node The removed child.
   */
  public function removeFirst() {
    $first = $this->children[0];
    $first->parent = NULL;
    array_splice($this->children, 0, 1);
    return $first;
  }

  /**
   * Remove the child from this node.
   * @param Node $child
   * @return Node The removed child
   */
  public function removeChild(Node $child) {
    $index = $this->indexOf($child);
    $child->parent = NULL;
    array_splice($this->children, $index, 1);
    return $child;
  }

  /**
   * Replace a child in this node.
   * @param Node $replacement
   * @param Node $old_child
   * @return Node The replaced old child.
   */
  public function replaceChild(Node $replacement, Node $old_child) {
    $index = $this->indexOf($old_child);
    $replacement->parent = $this;
    $old_child->parent = NULL;
    array_splice($this->children, $index, 1, array($replacement));
    return $old_child;
  }

  /**
   * Filters children to find matching nodes.
   * @param string $type
   *   Type of nodes to return.
   * @return Node[] matching children
   */
  public function filter($type) {
    $matches = array();
    foreach ($this->children as $child) {
      if ($child instanceof $type) {
        $matches[] = $child;
      }
    }
    return $matches;
  }

  /**
   * Find descendants that match given type.
   * @param string $type
   *   Type of nodes to return.
   * @return Node[] matching descendants
   */
  public function find($type) {
    $matches = array();
    if ($this instanceof $type) {
      $matches[] = $this;
    }
    foreach ($this->children as $child) {
      if ($child instanceof $type) {
        $matches[] = $child;
      }
      if ($child instanceof ParentNode) {
        $matches = array_merge($matches, $child->find($type));
      }
    }
    return $matches;
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
