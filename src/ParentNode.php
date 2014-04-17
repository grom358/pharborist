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
   * @var array
   */
  protected $properties = array();

  /**
   * Get the number of children.
   * @return int
   */
  public function childCount() {
    return $this->childCount;
  }

  /**
   * Return the first child.
   * @return Node
   */
  public function firstChild() {
    return $this->head;
  }

  /**
   * Return the last child.
   * @return Node
   */
  public function lastChild() {
    return $this->tail;
  }

  /**
   * Get the immediate children of this node.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function children(callable $callback = NULL) {
    $matches = array();
    $child = $this->head;
    while ($child) {
      if ($callback === NULL || $callback($child)) {
        $matches[] = $child;
      }
      $child = $child->next;
    }
    return new NodeCollection($matches);
  }

  /**
   * Prepend a child to node.
   * @param Node $node
   * @return $this
   */
  protected function prependChild(Node $node) {
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
   * Prepend children to this node.
   * @param Node|Node[]|NodeCollection $nodes
   * @return $this
   * @throws \InvalidArgumentException
   */
  public function prepend($nodes) {
    if ($nodes instanceof Node) {
      $this->prependChild($nodes);
    }
    elseif ($nodes instanceof NodeCollection) {
      foreach ($nodes->reverse() as $node) {
        $this->prependChild($node);
      }
    }
    elseif (is_array($nodes)) {
      foreach (array_reverse($nodes) as $node) {
        $this->prependChild($node);
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  /**
   * Append a child to node.
   * @param Node $node
   * @param string $property_name
   * @return $this
   */
  public function appendChild(Node $node, $property_name = NULL) {
    if ($this->tail === NULL) {
      $this->prependChild($node);
    }
    else {
      $this->insertAfterChild($this->tail, $node);
      $this->tail = $node;
    }
    if ($property_name !== NULL) {
      if (array_key_exists($property_name, $this->properties) && is_array($this->properties[$property_name])) {
        $this->properties[$property_name][] = $node;
      }
      else {
        $this->properties[$property_name] = $node;
      }
    }
    return $this;
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
    foreach ($node->properties as $name => $value) {
      $this->properties[$name] = $value;
    }
  }

  /**
   * Prepend children to this node.
   * @param Node|Node[]|NodeCollection $nodes
   * @return $this
   * @throws \InvalidArgumentException
   */
  public function append($nodes) {
    if ($nodes instanceof Node) {
      $this->appendChild($nodes);
    }
    elseif ($nodes instanceof NodeCollection || is_array($nodes)) {
      foreach ($nodes as $node) {
        $this->appendChild($node);
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
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
    foreach ($this->properties as $name => $value) {
      if (is_array($value)) {
        foreach ($value as $k => $v) {
          if ($child === $v) {
            unset($this->properties[$name][$k]);
            break 2;
          }
        }
      }
      elseif ($child === $value) {
        $this->properties[$name] = NULL;
        break;
      }
    }
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
    $child->parent = NULL;
    $child->previous = NULL;
    $child->next = NULL;
    return $this;
  }

  /**
   * Replace a child node.
   * @param Node $child
   * @param Node $replacement
   * @return $this
   */
  protected function replaceChild(Node $child, Node $replacement) {
    foreach ($this->properties as $name => $value) {
      if (is_array($value)) {
        foreach ($value as $k => $v) {
          if ($child === $v) {
            $this->properties[$name][$k] = $replacement;
            break 2;
          }
        }
      }
      elseif ($child === $value) {
        $this->properties[$name] = $replacement;
        break;
      }
    }
    $replacement->parent = $this;
    $replacement->previous = $child->previous;
    $replacement->next = $child->next;
    if ($child->previous === NULL) {
      $this->head = $replacement;
    }
    else {
      $child->previous->next = $replacement;
    }
    if ($child->next === NULL) {
      $this->tail = $replacement;
    }
    else {
      $child->next->previous = $replacement;
    }
    $child->parent = NULL;
    $child->previous = NULL;
    $child->next = NULL;
    return $this;
  }

  /**
   * Get the first (i.e. leftmost leaf) token.
   * @return TokenNode
   */
  public function firstToken() {
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
  public function lastToken() {
    $tail = $this->tail;
    while ($tail instanceof ParentNode) {
      $tail = $tail->tail;
    }
    return $tail;
  }

  /**
   * Test if the node has a descendant that matches.
   * @param callable $callback Callback to test for match.
   * @return NodeCollection
   */
  public function has(callable $callback) {
    $child = $this->head;
    while ($child) {
      if ($child instanceof ParentNode && $child->has($callback)) {
        return TRUE;
      }
      elseif ($callback($child)) {
        return TRUE;
      }
      $child = $child->next;
    }
    return FALSE;
  }

  /**
   * Find descendants that pass filter callback.
   * @param callable $callback Callback to filter by.
   * @return NodeCollection
   */
  public function find(callable $callback) {
    $matches = array();
    $child = $this->head;
    while ($child) {
      if ($callback($child)) {
        $matches[] = $child;
      }
      if ($child instanceof ParentNode) {
        $matches = array_merge($matches, $child->find($callback)->toArray());
      }
      $child = $child->next;
    }
    return new NodeCollection($matches);
  }

  public function getSourcePosition() {
    if ($this->head === NULL) {
      return $this->parent->getSourcePosition();
    }
    $child = $this->head;
    return $child->getSourcePosition();
  }

  public function __clone() {
    // Clone does not belong to a parent.
    $this->parent = NULL;
    $this->previous = NULL;
    $this->next = NULL;
    list($this->head, $this->properties) = unserialize(serialize(array($this->head, $this->properties)));
  }

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
