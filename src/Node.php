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
        return new NodeCollection($parents);
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
    foreach ($nodes as $node) {
      $node->remove();
      $this->parent->insertBeforeChild($this, $node);
    }
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
      $this->parent->replaceChild($this, $nodes);
    }
    elseif ($nodes instanceof NodeCollection || is_array($nodes)) {
      $first = TRUE;
      $insert_after = $this;
      foreach ($nodes as $node) {
        if ($first) {
          $this->parent->replaceChild($this, $node);
          $first = FALSE;
        }
        else {
          $insert_after->parent->insertAfter($insert_after, $node);
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
      $targets->parent->replaceChild($targets, $this);
    }
    elseif ($targets instanceof NodeCollection || is_array($targets)) {
      foreach ($targets as $target) {
        $target->parent->replaceChild($target, clone $this);
      }
    }
    else {
      throw new \InvalidArgumentException();
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
      $targets->prependChild($this);
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

  abstract public function getSourcePosition();
}
