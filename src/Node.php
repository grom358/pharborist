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

  public function parent(callable $callback = NULL) {
    if ($callback) {
      return $callback($this->parent) ? $this->parent : NULL;
    }
    else {
      return $this->parent;
    }
  }

  public function parents(callable $callback = NULL) {
    $parents = [];
    $parent = $this->parent;
    while ($parent) {
      if ($callback === NULL || $callback($parent)) {
        $parents[] = $parent;
      }
      $parent = $parent->parent;
    }
    return new NodeCollection($parents);
  }

  public function parentsUntil(callable $callback, $inclusive = FALSE) {
    $parents = [];
    $parent = $this->parent;
    while ($parent) {
      if ($callback($parent)) {
        if ($inclusive) {
          $parents[] = $parent;
        }
        break;
      }
      $parents[] = $parent;
      $parent = $parent->parent;
    }
    return new NodeCollection($parents);
  }

  public function closest(callable $callback) {
    if ($callback($this)) {
      return $this;
    }
    $parent = $this->parent;
    while ($parent) {
      if ($callback($parent)) {
        return $parent;
      }
      $parent = $parent->parent;
    }
    return NULL;
  }

  public function previous(callable $callback = NULL) {
    if ($callback) {
      return $callback($this->previous) ? $this->previous : NULL;
    }
    else {
      return $this->previous;
    }
  }

  public function previousAll(callable $callback = NULL) {
    $matches = [];
    $previous = $this->previous;
    while ($previous) {
      if ($callback === NULL || $callback($previous)) {
        $matches[] = $previous;
      }
      $previous = $previous->previous;
    }
    return new NodeCollection(array_reverse($matches));
  }

  public function previousUntil(callable $callback, $inclusive = FALSE) {
    $matches = [];
    $previous = $this->previous;
    while ($previous) {
      if ($callback($previous)) {
        if ($inclusive) {
          $matches[] = $previous;
        }
        break;
      }
      $matches[] = $previous;
      $previous = $previous->previous;
    }
    return new NodeCollection(array_reverse($matches));
  }

  public function next(callable $callback = NULL) {
    if ($callback) {
      return $callback($this->next) ? $this->next : NULL;
    }
    else {
      return $this->next;
    }
  }

  public function nextAll(callable $callback = NULL) {
    $matches = [];
    $next = $this->next;
    while ($next) {
      if ($callback === NULL || $callback($next)) {
        $matches[] = $next;
      }
      $next = $next->next;
    }
    return new NodeCollection($matches);
  }

  public function nextUntil(callable $callback, $inclusive = FALSE) {
    $matches = [];
    $next = $this->next;
    while ($next) {
      if ($callback($next)) {
        if ($inclusive) {
          $matches[] = $next;
        }
        break;
      }
      $matches[] = $next;
      $next = $next->next;
    }
    return new NodeCollection($matches);
  }

  public function insertBefore($targets) {
    $this->remove();
    if ($targets instanceof Node) {
      $targets->parent->insertBeforeChild($targets, $this);
    }
    elseif ($targets instanceof NodeCollection || is_array($targets)) {
      $first = TRUE;
      foreach ($targets as $target) {
        $target->parent->insertBeforeChild($target, $first ? $this : clone $this);
        $first = FALSE;
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  public function before($nodes) {
    if ($nodes instanceof Node) {
      $nodes->remove();
      $this->parent->insertBeforeChild($this, $nodes);
    }
    elseif ($nodes instanceof NodeCollection || is_array($nodes)) {
      /** @var Node $node */
      foreach ($nodes as $node) {
        $node->remove();
        $this->parent->insertBeforeChild($this, $node);
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  public function insertAfter($targets) {
    $this->remove();
    if ($targets instanceof Node) {
      $targets->parent->insertAfterChild($targets, $this);
    }
    elseif ($targets instanceof NodeCollection || is_array($targets)) {
      $first = TRUE;
      foreach ($targets as $target) {
        $target->parent->insertAfterChild($target, $first ? $this : clone $this);
        $first = FALSE;
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  public function after($nodes) {
    if ($nodes instanceof Node) {
      $this->parent->insertAfterChild($this, $nodes);
    }
    elseif ($nodes instanceof NodeCollection || is_array($nodes)) {
      $insert_after = $this;
      foreach ($nodes as $node) {
        $insert_after->parent->insertAfterChild($insert_after, $node);
        $insert_after = $node;
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  public function remove() {
    if ($this->parent) {
      $this->parent->removeChild($this);
    }
    return $this;
  }

  public function replaceWith($nodes) {
    if (!$this->parent) {
      return $this;
    }
    if ($nodes instanceof Node) {
      $nodes->remove();
      $this->parent->replaceChild($this, $nodes);
    }
    elseif ($nodes instanceof NodeCollection || is_array($nodes)) {
      $first = TRUE;
      $insert_after = NULL;
      /** @var Node $node */
      foreach ($nodes as $node) {
        $node->remove();
        if ($first) {
          $this->parent->replaceChild($this, $node);
          $insert_after = $node;
          $first = FALSE;
        }
        else {
          $insert_after->parent->insertAfterChild($insert_after, $node);
          $insert_after = $node;
        }
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  public function replaceAll($targets) {
    $this->remove();
    if ($targets instanceof Node) {
      if ($targets->parent) {
        $targets->parent->replaceChild($targets, $this);
      }
    }
    elseif ($targets instanceof NodeCollection || is_array($targets)) {
      $first = TRUE;
      foreach ($targets as $target) {
        if ($target->parent) {
          $target->parent->replaceChild($target, $first ? $this : clone $this);
        }
        $first = FALSE;
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  public function swapWith(Node $replacement) {
    $parent = $this->parent;
    if ($this->next === $replacement) {
      // Nodes are adjacent
      $previous = $this->previous;
      $next = $this;
      $replacement_previous = $replacement;
      $replacement_next = $replacement->next;
    }
    elseif ($replacement === $this->previous) {
      // Nodes are adjacent
      $previous = $this;
      $next = $this->next;
      $replacement_previous = $replacement->previous;
      $replacement_next = $replacement;
    }
    else {
      $previous = $this->previous;
      $next = $this->next;
      $replacement_previous = $replacement->previous;
      $replacement_next = $replacement->next;
    }
    $replacement_head = $replacement_tail = FALSE;
    if ($replacement->parent) {
      $replacement_head = $replacement->parent->head === $replacement;
      $replacement_tail = $replacement->parent->tail === $replacement;
    }
    if ($this->parent) {
      if ($this->parent->head === $this) {
        $this->parent->head = $replacement;
      }
      if ($this->parent->tail === $this) {
        $this->parent->tail = $replacement;
      }
    }
    $this->parent = $replacement->parent;
    $this->previous = $replacement_previous;
    if ($this->previous) {
      $this->previous->next = $this;
    }
    $this->next = $replacement_next;
    if ($this->next) {
      $this->next->previous = $this;
    }
    if ($replacement_head) {
      $replacement->parent->head = $this;
    }
    if ($replacement_tail) {
      $replacement->parent->tail = $this;
    }
    $replacement->parent = $parent;
    $replacement->previous = $previous;
    if ($replacement->previous) {
      $replacement->previous->next = $replacement;
    }
    $replacement->next = $next;
    if ($replacement->next) {
      $replacement->next->previous = $replacement;
    }
    return $this;
  }

  public function prependTo($targets) {
    $this->remove();
    if ($targets instanceof ParentNode) {
      $targets->prependChild($this);
    }
    elseif ($targets instanceof NodeCollection || is_array($targets)) {
      $first = TRUE;
      foreach ($targets as $target) {
        if ($target instanceof ParentNode) {
          $target->prependChild($first ? $this : clone $this);
          $first = FALSE;
        }
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  public function appendTo($targets) {
    $this->remove();
    if ($targets instanceof ParentNode) {
      $targets->appendChild($this);
    }
    elseif ($targets instanceof NodeCollection || is_array($targets)) {
      $first = TRUE;
      foreach ($targets as $target) {
        if ($target instanceof ParentNode) {
          $target->appendChild($first ? $this : clone $this);
          $first = FALSE;
        }
      }
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }

  public function __clone() {
    // Clone does not belong to any parent.
    $this->parent = NULL;
    $this->previous = NULL;
    $this->next = NULL;
  }
}
