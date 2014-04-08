<?php
namespace Pharborist;

/**
 * A node that has children.
 */
abstract class ParentNode extends Node {
  /**
   * @var Node
   */
  protected $head;

  /**
   * @var Node
   */
  protected $tail;

  /**
   * @var int
   */
  protected $childCount;

  /**
   * Get the number of children.
   * @return int
   */
  public function getChildCount() {
    return $this->childCount;
  }

  /**
   * Return the first child.
   * @return Node
   */
  public function getFirst() {
    return $this->head;
  }

  /**
   * Return the last child.
   * @return Node
   */
  public function getLast() {
    return $this->tail;
  }

  /**
   * Prepend a child to node.
   * @param Node $node
   * @return $this
   */
  public function prependChild(Node $node) {
    if ($this->head === NULL) {
      $this->childCount++;
      $node->parent = $this;
      $node->previous = NULL;
      $node->next = NULL;
      $this->head = $this->tail = $node;
    }
    else {
      $this->insertBeforeChild($this->head, $node);
      $this->head = $node;
    }
    return $this;
  }

  /**
   * Append a child to node.
   * @param Node $node
   * @return Node
   */
  public function appendChild(Node $node) {
    if ($this->tail === NULL) {
      $this->prependChild($node);
    }
    else {
      $this->insertAfterChild($this->tail, $node);
      $this->tail = $node;
    }
    return $node;
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
    $child = $node->head;
    while ($child) {
      $next = $child->next;
      $this->appendChild($child);
      $child = $next;
    }
  }

  /**
   * Insert a node before a child.
   * @param Node $child
   * @param Node $node
   * @return $this
   */
  protected function insertBeforeChild(Node $child, Node $node) {
    $this->childCount++;
    $node->parent = $this;
    if ($child->previous === NULL) {
      $this->head = $node;
    }
    else {
      $child->previous->next = $node;
    }
    $node->previous = $child->previous;
    $node->next = $child;
    $child->previous = $node;
    return $this;
  }

  /**
   * Insert a node after a child.
   * @param Node $child
   * @param Node $node
   * @return $this
   */
  protected function insertAfterChild(Node $child, Node $node) {
    $this->childCount++;
    $node->parent = $this;
    if ($child->next === NULL) {
      $this->tail = $node;
    }
    else {
      $child->next->previous = $node;
    }
    $node->previous = $child;
    $node->next = $child->next;
    $child->next = $node;
    return $this;
  }

  /**
   * Remove a child node.
   * @param Node $child
   * @return $this
   */
  protected function removeChild(Node $child) {
    $this->childCount--;
    if ($child->previous === NULL) {
      $this->head = $child->next;
    }
    else {
      $child->previous->next = $child->next;
    }
    if ($child->next === NULL) {
      $this->tail = $child->previous;
    }
    else {
      $child->next->previous = $child->previous;
    }
    $child->previous = NULL;
    $child->next = NULL;
    return $this;
  }

  /**
   * Remove the first child.
   * @return Node The removed child.
   */
  public function removeFirst() {
    $head = $this->head;
    if ($head) {
      $this->removeChild($head);
    }
    return $head;
  }

  /**
   * Replace a child node.
   * @param Node $child
   * @param Node $replacement
   * @return $this
   */
  protected function replaceChild(Node $child, Node $replacement) {
    $this->insertBeforeChild($child, $replacement);
    $this->removeChild($child);
    return $this;
  }

  /**
   * Filters children to find matching nodes.
   * @param string $type
   *   Type of nodes to return.
   * @return Node[] matching children
   */
  public function filter($type) {
    $matches = array();
    $child = $this->head;
    while ($child) {
      if ($child instanceof $type) {
        $matches[] = $child;
      }
      $child = $child->next;
    }
    return $matches;
  }

  /**
   * Get the first (i.e. leftmost leaf) token.
   * @return TokenNode
   */
  public function getFirstToken() {
    $head = $this->head;
    while ($head instanceof ParentNode) {
      $head = $head->head;
    }
    return $head;
  }

  /**
   * Get the last (i.e. rightmost leaf) token.
   * @return TokenNode
   */
  public function getLastToken() {
    $tail = $this->tail;
    while ($tail instanceof ParentNode) {
      $tail = $tail->tail;
    }
    return $tail;
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
    $child = $this->head;
    while ($child) {
      if ($child instanceof $type) {
        $matches[] = $child;
      }
      if ($child instanceof ParentNode) {
        $matches = array_merge($matches, $child->find($type));
      }
      $child = $child->next;
    }
    return $matches;
  }

  /**
   * @return SourcePosition
   */
  public function getSourcePosition() {
    if ($this->head === NULL) {
      return $this->parent->getSourcePosition();
    }
    $child = $this->head;
    return $child->getSourcePosition();
  }

  /**
   * @return string
   */
  public function __toString() {
    $str = '';
    $child = $this->head;
    while ($child) {
      $str .= (string) $child;
      $child = $child->next;
    }
    return $str;
  }
}
