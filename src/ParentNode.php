<?php
namespace Pharborist;

/**
 * A node that has children.
 */
abstract class ParentNode extends Node implements ParentNodeInterface {
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
  protected $childCount = 0;

  public function getChildProperties() {
    $properties = get_object_vars($this);
    unset($properties['head']);
    unset($properties['tail']);
    unset($properties['childCount']);
    unset($properties['parent']);
    unset($properties['previous']);
    unset($properties['next']);
    return $properties;
  }

  public function isEmpty() {
    return $this->childCount === 0;
  }

  public function childCount() {
    return $this->childCount;
  }

  public function firstChild() {
    return $this->head;
  }

  public function lastChild() {
    return $this->tail;
  }

  /**
   * Get children that are instance of class.
   * @param string $class_name
   * @return Node[]
   */
  protected function childrenByInstance($class_name) {
    $matches = [];
    $child = $this->head;
    while ($child) {
      if ($child instanceof $class_name) {
        $matches[] = $child;
      }
      $child = $child->next;
    }
    return $matches;
  }

  public function children(callable $callback = NULL) {
    $matches = [];
    $child = $this->head;
    while ($child) {
      if ($callback === NULL || $callback($child)) {
        $matches[] = $child;
      }
      $child = $child->next;
    }
    return new NodeCollection($matches, FALSE);
  }

  public function clear() {
    $this->head = $this->tail = NULL;
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
   * @return $this
   */
  protected function appendChild(Node $node) {
    if ($this->tail === NULL) {
      $this->prependChild($node);
    }
    else {
      $this->insertAfterChild($this->tail, $node);
      $this->tail = $node;
    }
    return $this;
  }

  /**
   * Add a child to node.
   *
   * Internal use only, used by parser when building a node.
   *
   * @param Node $node
   * @param string $property_name
   * @return $this
   */
  public function addChild(Node $node, $property_name = NULL) {
    $this->appendChild($node);
    if ($property_name !== NULL) {
      $this->{$property_name} = $node;
    }
    return $this;
  }

  /**
   * Add children to node.
   *
   * Internal use only, used by parser when building a node.
   *
   * @param Node[] $children
   */
  public function addChildren(array $children) {
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
    foreach ($node->getChildProperties() as $name => $value) {
      $this->{$name} = $value;
    }
  }

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
    foreach ($this->getChildProperties() as $name => $value) {
      if ($child === $value) {
        $this->{$name} = NULL;
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
    foreach ($this->getChildProperties() as $name => $value) {
      if ($child === $value) {
        $this->{$name} = $replacement;
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
   * {@inheritDoc}
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
   * {@inheritDoc}
   * @return TokenNode
   */
  public function lastToken() {
    $tail = $this->tail;
    while ($tail instanceof ParentNode) {
      $tail = $tail->tail;
    }
    return $tail;
  }

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

  public function isDescendant(Node $node) {
    $parent = $node->parent;
    while ($parent) {
      if ($parent === $this) {
        return TRUE;
      }
      $parent = $parent->parent;
    }
    return FALSE;
  }

  private function _find(&$matches, callable $callback) {
    $child = $this->head;
    while ($child) {
      if ($callback($child)) {
        $matches[] = $child;
      }
      if ($child instanceof ParentNode) {
        $child->_find($matches, $callback);
      }
      $child = $child->next;
    }
  }

  public function find(callable $callback) {
    $matches = [];
    $this->_find($matches, $callback);
    return new NodeCollection($matches, FALSE);
  }

  public function walk(callable $callback) {
    $ret = $callback($this);
    if ($ret === FALSE) {
      return;
    }
    $child = $this->head;
    while($child) {
      if($child instanceof ParentNode) {
        $child->walk($callback);
      }
      else {
        $callback($child);
      }
      $child = $child->next;
    }
  }

  public function acceptVisitor(VisitorInterface $visitor) {
    $visitor->visit($this);
    $child = $this->head;
    while($child) {
      if($child instanceof ParentNode) {
        $child->acceptVisitor($visitor);
      }
      else {
        $visitor->visit($child);
      }
      $child = $child->next;
    }
    $visitor->visitEnd($this);
  }

  public function getFilename() {
    if ($this->head === NULL) {
      return $this->parent->getFilename();
    }
    $child = $this->head;
    return $child->getFilename();
  }

  public function getLineNumber() {
    if ($this->head === NULL) {
      return $this->parent->getLineNumber();
    }
    $child = $this->head;
    return $child->getLineNumber();
  }

  public function getNewlineCount() {
    return substr_count($this->getText(), "\n");
  }

  public function getColumnNumber() {
    if ($this->head === NULL) {
      return $this->parent->getColumnNumber();
    }
    $child = $this->head;
    return $child->getColumnNumber();
  }

  public function getByteOffset() {
    if ($this->head === NULL) {
      return $this->parent->getByteOffset();
    }
    $child = $this->head;
    return $child->getByteOffset();
  }

  public function getByteLength() {
    return strlen($this->getText());
  }

  public function __clone() {
    // Clone does not belong to a parent.
    $this->parent = NULL;
    $this->previous = NULL;
    $this->next = NULL;
    $properties = $this->getChildProperties();
    $children = [];
    $child = $this->head;
    while ($child) {
      $key = array_search($child, $properties, TRUE);
      if ($key !== FALSE) {
        $children[$key] = clone $child;
      }
      else {
        $children[] = clone $child;
      }
      $child = $child->next;
    }
    $keys = array_keys($children);
    $this->head = empty($children) ? NULL : $children[$keys[0]];
    $this->tail = $this->head;
    /** @var Node $prev */
    $prev = NULL;
    foreach ($children as $key => $child) {
      if (!is_int($key)) {
        $this->{$key} = $child;
      }
      $this->tail = $child;
      $child->parent = $this;
      $child->previous = $prev;
      if ($prev) {
        $prev->next = $child;
      }
      $prev = $child;
    }
  }

  public function getText() {
    $str = '';
    $child = $this->head;
    while ($child) {
      $str .= $child->getText();
      $child = $child->next;
    }
    return $str;
  }

  public function __toString() {
    return $this->getText();
  }

  /**
   * Convert tree into array.
   *
   * Useful for viewing the tree structure.
   */
  public function getTree() {
    $children = array();
    $properties = $this->getChildProperties();
    $child = $this->head;
    $i = 0;
    while ($child) {
      $key = array_search($child, $properties, TRUE);
      if (!$key) {
        $key = $i;
      }
      if ($child instanceof ParentNode) {
        $children[$key] = $child->getTree();
      }
      else {
        /** @var TokenNode $child */
        $children[$key] = array($child->getTypeName() => $child->getText());
      }
      $child = $child->next;
      $i++;
    }
    $class_name = get_class($this);
    return array($class_name => $children);
  }
}
