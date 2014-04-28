<?php
namespace Pharborist;

/**
 * A set of matched nodes.
 *
 * jQuery like wrapper around Node[] to support Traversing and Manipulation.
 */
class NodeCollection implements \IteratorAggregate, \Countable, \ArrayAccess {
  /**
   * @var Node[]
   */
  protected $nodes;

  /**
   * Sort nodes and remove duplicates
   * @param Node[] $nodes
   * @return Node[]
   */
  protected static function sortUnique($nodes) {
    $sort = [];
    foreach ($nodes as $node) {
      $key = $node->sortKey();
      $sort[$key] = $node;
    }
    ksort($sort);
    return array_values($sort);
  }

  public function __construct($nodes, $sort = TRUE) {
    $this->nodes = $sort ? static::sortUnique($nodes) : $nodes;
  }

  public function getIterator() {
    return new \ArrayIterator($this->nodes);
  }

  public function count() {
    return count($this->nodes);
  }

  public function offsetExists($offset) {
    return isset($this->nodes[$offset]);
  }

  public function offsetGet($offset) {
    return $this->nodes[$offset];
  }

  public function offsetSet($offset, $value) {
    throw new \BadMethodCallException('NodeCollection offsetSet not supported');
  }

  public function offsetUnset($offset) {
    throw new \BadMethodCallException('NodeCollection offsetUnset not supported');
  }

  /**
   * Get collection in reverse order
   * @return Node[]
   */
  public function reverse() {
    return array_reverse($this->nodes);
  }

  /**
   * Reduce the set of matched nodes to a subset specified by a range.
   * @param int $start_index
   * @param int $end_index
   * @return NodeCollection
   */
  public function slice($start_index, $end_index = NULL) {
    $length = $end_index ? $end_index - $start_index + 1 : NULL;
    return new NodeCollection(array_slice($this->nodes, $start_index, $length));
  }

  /**
   * Apply callback to each node in the matched set.
   * @param callable $callback Callback to apply.
   * @return NodeCollection
   */
  public function map(callable $callback) {
    return new NodeCollection(array_map($callback, $this->nodes));
  }

  /**
   * @return Node[]
   */
  public function toArray() {
    return $this->nodes;
  }

  /**
   * Get the parent of each node in the current set of matched nodes,
   * optionally filtered by a callback.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function parent(callable $callback = NULL) {
    $matches = [];
    foreach ($this->nodes as $node) {
      if ($match = $node->parent($callback)) {
        $matches[] = $match;
      }
    }
    return new NodeCollection($matches);
  }

  /**
   * Get the ancestors of each node in the current set of matched nodes,
   * optionally filtered by a callback.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function parents(callable $callback = NULL) {
    $matches = [];
    foreach ($this->nodes as $node) {
      $matches = array_merge($matches, $node->parents($callback)->nodes);
    }
    return new NodeCollection($matches);
  }

  /**
   * Get ancestors of each node in the current set of matched nodes,
   * up to the node matched by callback.
   * @param callable $callback Callback to test for match.
   * @param bool $inclusive TRUE to include the node matched by callback.
   * @return NodeCollection
   */
  public function parentsUntil(callable $callback, $inclusive = FALSE) {
    $matches = [];
    foreach ($this->nodes as $node) {
      $matches = array_merge($matches, $node->parentsUntil($callback, $inclusive)->nodes);
    }
    return new NodeCollection($matches);
  }

  /**
   * For each node in the collection, get the first node matched by the
   * callback by testing this node and traversing up through its ancestors in
   * the tree.
   * @param callable $callback Callback to test for match.
   * @return Node
   */
  public function closest(callable $callback) {
    $matches = [];
    foreach ($this->nodes as $node) {
      if ($match = $node->closest($callback)) {
        $matches[] = $match;
      }
    }
    return new NodeCollection($matches);
  }

  /**
   * Get the immediately preceding sibling of each node in the set of matched
   * nodes. If a callback is provided, it retrieves the next sibling only if
   * the callback returns TRUE.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function previous(callable $callback = NULL) {
    $matches = [];
    foreach ($this->nodes as $node) {
      if ($match = $node->previous($callback)) {
        $matches[] = $match;
      }
    }
    return new NodeCollection($matches);
  }

  /**
   * Get all preceding siblings of each node in the set of matched nodes,
   * optionally filtered by a callback.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function previousAll(callable $callback = NULL) {
    $matches = [];
    foreach ($this->nodes as $node) {
      $matches = array_merge($matches, $node->previousAll($callback)->nodes);
    }
    return new NodeCollection($matches);
  }

  /**
   * Get all preceding siblings of each node up to the node matched by the
   * callback.
   * @param callable $callback Callback to test for match.
   * @param bool $inclusive TRUE to include the node matched by callback.
   * @return NodeCollection
   */
  public function previousUntil(callable $callback, $inclusive = FALSE) {
    $matches = [];
    foreach ($this->nodes as $node) {
      $matches = array_merge($matches, $node->previousUntil($callback, $inclusive)->nodes);
    }
    return new NodeCollection($matches);
  }

  /**
   * Get the immediately following sibling of each node in the set of matched
   * nodes. If a callback is provided, it retrieves the next sibling only if
   * the callback returns TRUE.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function next(callable $callback = NULL) {
    $matches = [];
    foreach ($this->nodes as $node) {
      if ($match = $node->next($callback)) {
        $matches[] = $match;
      }
    }
    return new NodeCollection($matches);
  }

  /**
   * Get all following siblings of each node in the set of matched nodes,
   * optionally filtered by a callback.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function nextAll(callable $callback = NULL) {
    $matches = [];
    foreach ($this->nodes as $node) {
      $matches = array_merge($matches, $node->nextAll($callback)->nodes);
    }
    return new NodeCollection($matches);
  }

  /**
   * Get all following siblings of each node up to the node matched by the
   * callback.
   * @param callable $callback Callback to test for match.
   * @param bool $inclusive TRUE to include the node matched by callback.
   * @return NodeCollection
   */
  public function nextUntil(callable $callback, $inclusive = FALSE) {
    $matches = [];
    foreach ($this->nodes as $node) {
      $matches = array_merge($matches, $node->nextUntil($callback, $inclusive)->nodes);
    }
    return new NodeCollection($matches);
  }

  /**
   * Get the descendants of each node in the current set of matched nodes,
   * filtered by callback.
   * @param callable $callback Callback to filter by.
   * @return NodeCollection
   */
  public function find(callable $callback) {
    $matches = [];
    foreach ($this->nodes as $node) {
      if ($node instanceof ParentNode) {
        $matches = array_merge($matches, $node->find($callback)->nodes);
      }
    }
    return new NodeCollection($matches);
  }

  /**
   * Reduce the set of matched nodes to those that pass the callback filter.
   * @param callable $callback Callback to test for match.
   * @return NodeCollection
   */
  public function filter(callable $callback) {
    $matches = [];
    foreach ($this->nodes as $index => $node) {
      if ($callback($node, $index)) {
        $matches[] = $node;
      }
    }
    return new NodeCollection($matches, FALSE);
  }

  /**
   * Remove nodes from the set of matched nodes.
   * @param callable $callback Callback to test for match.
   * @return NodeCollection
   */
  public function not(callable $callback) {
    $matches = [];
    foreach ($this->nodes as $index => $node) {
      if (!$callback($node, $index)) {
        $matches[] = $node;
      }
    }
    return new NodeCollection($matches, FALSE);
  }

  /**
   * Reduce the set of matched nodes to those that have a descendant that
   * match.
   * @param callable $callback Callback to test for match.
   * @return NodeCollection
   */
  public function has(callable $callback) {
    $matches = [];
    foreach ($this->nodes as $node) {
      if ($node instanceof ParentNode && $node->has($callback)) {
        $matches[] = $node;
      }
    }
    return new NodeCollection($matches, FALSE);
  }

  /**
   * Reduce the set of matched nodes to the first in the set.
   */
  public function first() {
    $matches = [];
    if (!empty($this->nodes)) {
      $matches[] = $this->nodes[0];
    }
    return new NodeCollection($matches, FALSE);
  }

  /**
   * Reduce the set of matched nodes to the last in the set.
   */
  public function last() {
    $matches = [];
    if (!empty($this->nodes)) {
      $matches[] = $this->nodes[count($this->nodes) - 1];
    }
    return new NodeCollection($matches, FALSE);
  }

  /**
   * Insert every node in the set of matched nodes before the targets.
   * @param Node|Node[]|NodeCollection $targets Nodes to insert before.
   * @return $this
   */
  public function insertBefore($targets) {
    foreach ($this->nodes as $node) {
      $node->insertBefore($targets);
    }
    return $this;
  }

  /**
   * Insert nodes before each node in the set of matched nodes.
   * @param Node|Node[]|NodeCollection $nodes Nodes to insert.
   * @return $this
   */
  public function before($nodes) {
    foreach ($this->nodes as $node) {
      $node->before($nodes);
    }
    return $this;
  }

  /**
   * Insert every node in the set of matched nodes after the targets.
   * @param Node|Node[]|NodeCollection $targets Nodes to insert after.
   * @return $this
   */
  public function insertAfter($targets) {
    foreach ($this->nodes as $node) {
      $node->insertAfter($targets);
    }
    return $this;
  }

  /**
   * Insert nodes after each node in the set of matched nodes.
   * @param Node|Node[]|NodeCollection $nodes Nodes to insert.
   * @return $this
   */
  public function after($nodes) {
    foreach ($this->nodes as $node) {
      $node->after($nodes);
    }
    return $this;
  }

  /**
   * Remove the set of matched nodes from the tree.
   * @return $this
   */
  public function remove() {
    foreach ($this->nodes as $node) {
      $node->remove();
    }
    return $this;
  }

  /**
   * Replace each node in the set of matched nodes with the provided new nodes
   * and return the set of nodes that was removed.
   * @param Node|Node[]|NodeCollection $nodes Replacement nodes.
   * @return $this
   */
  public function replaceWith($nodes) {
    $first = TRUE;
    foreach ($this->nodes as $node) {
      if (!$first) {
        if (is_array($nodes)) {
          $nodes = new NodeCollection($nodes, FALSE);
        }
        $nodes = clone $nodes;
      }
      $node->replaceWith($nodes);
      $first = FALSE;
    }
    return $this;
  }

  /**
   * Replace each target node with the set of matched nodes.
   * @param Node|Node[]|NodeCollection $targets Targets to replace.
   * @return $this
   */
  public function replaceAll($targets) {
    if ($targets instanceof Node) {
      $targets->replaceWith($this->nodes);
    }
    elseif ($targets instanceof NodeCollection || is_array($targets)) {
      $first = TRUE;
      /** @var Node $target */
      foreach ($targets as $target) {
        $target->replaceWith($first ? $this->nodes : clone $this);
        $first = FALSE;
      }
    }
    return $this;
  }

  /**
   * Add nodes to this collection.
   * @param Node|Node[]|NodeCollection $nodes Nodes to add to collection.
   * @return $this
   * @throws \InvalidArgumentException
   */
  public function add($nodes) {
    if ($nodes instanceof Node) {
      $this->nodes[] = $nodes;
    }
    elseif ($nodes instanceof NodeCollection) {
      $this->nodes = array_merge($this->nodes, $nodes->nodes);
    }
    elseif (is_array($nodes)) {
      $this->nodes = array_merge($this->nodes, $nodes);
    }
    else {
      throw new \InvalidArgumentException();
    }
    $this->nodes = static::sortUnique($this->nodes);
    return $this;
  }

  public function __clone() {
    $copy = [];
    foreach ($this->nodes as $node) {
      $copy[] = clone $node;
    }
    $this->nodes = $copy;
  }
}
