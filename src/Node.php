<?php
namespace Pharborist;

/**
 * @mainpage
 *
 * @section Nodes
 * A node is the basic building block of a PHP syntax tree. It represents a
 * single cohesive piece of code. Nodes are made up one or more tokens, which
 * are individual characters or strings which mean something to the PHP
 * interpreter. Pharborist provides a node class for just about every kind of
 * statement or expression you can write in PHP.
 */
use Pharborist\Types\ArrayNode;
use Pharborist\Types\ArrayPairNode;
use Pharborist\Types\BooleanNode;
use Pharborist\Types\FloatNode;
use Pharborist\Types\IntegerNode;
use Pharborist\Types\NullNode;
use Pharborist\Types\StringNode;

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
    return new NodeCollection(array_reverse($parents), FALSE);
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
    return new NodeCollection(array_reverse($parents), FALSE);
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

  public function furthest(callable $callback) {
    $match = NULL;
    if ($callback($this)) {
      $match = $this;
    }
    $parent = $this->parent;
    while ($parent) {
      if ($callback($parent)) {
        $match = $parent;
      }
      $parent = $parent->parent;
    }
    return $match;
  }

  public function index() {
    $index = 0;
    $child = $this->parent->head;
    while ($child) {
      if ($child === $this) {
        return $index;
      }
      $child = $child->next;
      $index++;
    }
    return -1;
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
    return new NodeCollection(array_reverse($matches), FALSE);
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
    return new NodeCollection(array_reverse($matches), FALSE);
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
    return new NodeCollection($matches, FALSE);
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
    return new NodeCollection($matches, FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function siblings(callable $callback = NULL) {
    return $this->previousAll($callback)->add($this->nextAll($callback));
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
      if ($nodes === $this) {
        return $this;
      }
      $nodes->remove();
      $this->parent->replaceChild($this, $nodes);
    }
    elseif ($nodes instanceof NodeCollection || is_array($nodes)) {
      $first = TRUE;
      $insert_after = NULL;
      /** @var Node $node */
      foreach ($nodes as $node) {
        if ($first) {
          if ($node !== $this) {
            $node->remove();
            $this->parent->replaceChild($this, $node);
          }
          $insert_after = $node;
          $first = FALSE;
        }
        else {
          $node->remove();
          $insert_after->parent->insertAfterChild($insert_after, $node);
          $insert_after = $node;
        }
      }
    }
    elseif (is_callable($nodes)) {
      $this->replaceWith($nodes($this));
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

  /**
   * Get a unique key for sorting this node.
   *
   * Used to sort nodes into tree order. That is top to bottom and then left
   * to right.
   *
   * @return string
   */
  public function sortKey() {
    if ($this instanceof RootNode) {
      return spl_object_hash($this);
    }
    if (!$this->parent) {
      return '~/' . spl_object_hash($this);
    }
    $path = $this->parent->sortKey() . '/';
    $position = 0;
    $previous = $this->previous;
    while ($previous) {
      $position++;
      $previous = $previous->previous;
    }
    $path .= $position;
    return $path;
  }

  /**
   * Tests this node against a condition.
   *
   * @param callable|string $test
   *  Either a callback function to test the node against, or a class name
   *  (wrapper around instanceof). Eventually this will accept a callable
   *  or a filter query (issue #61).
   *
   * @return boolean
   *
   * @throws \InvalidArgumentException
   */
  public function is($test) {
    if (is_callable($test)) {
      return (boolean) $test($this);
    }
    elseif (is_string($test)) {
      return $this->is(Filter::isInstanceOf($test));
    }
    else {
      throw new \InvalidArgumentException();
    }
  }

  /**
   * Tests if this node matches any the tests in the array.
   *
   * @param string|callable[] $tests
   *
   * @return boolean
   */
  public function isAnyOf(array $tests) {
    foreach ($tests as $test) {
      if ($this->is($test)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Tests if this node matches all of the tests in the array.
   *
   * @param string|callable[] $tests
   *
   * @return boolean
   */
  public function isAllOf(array $tests) {
    foreach ($tests as $test) {
      if (! $this->is($test)) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Returns the code block containing this node. The code block could be a
   * control structure (if statement, for loop, case statement, etc.), a
   * function, a class definition, or the whole syntax tree.
   *
   * @return StatementBlockNode|RootNode|NULL
   */
  public function getLogicalBlock() {
    return $this->closest(function(Node $node) {
      return $node instanceof StatementBlockNode || $node instanceof RootNode;
    });
  }

  /**
   * Creates a Node from a php value.
   *
   * @param string|integer|float|boolean|array|null $value
   *  The value to create a node for.
   *
   * @return FloatNode|IntegerNode|StringNode|BooleanNode|NullNode|ArrayNode
   *
   * @throws \InvalidArgumentException if $value is not a scalar.
   */
  public static function fromValue($value) {
    if (is_array($value)) {
      $elements = [];
      foreach ($value as $k => $v) {
        $elements[] = ArrayPairNode::create(static::fromValue($k), static::fromValue($v));
      }
      return ArrayNode::create($elements);
    }
    elseif (is_string($value)) {
      return StringNode::create(var_export($value, TRUE));
    }
    elseif (is_integer($value)) {
      return new IntegerNode(T_LNUMBER, $value);
    }
    elseif (is_float($value)) {
      return new FloatNode(T_DNUMBER, $value);
    }
    elseif (is_bool($value)) {
      return BooleanNode::create($value);
    }
    elseif (is_null($value)) {
      return NullNode::create('NULL');
    }
    else {
      throw new \InvalidArgumentException();
    }
  }

  /**
   * Returns the statement (or statement block) which contains this node, if
   * it's part of a statement.
   *
   * @return StatementNode|NULL
   */
  public function getStatement() {
    return $this->closest(Filter::isInstanceOf('\Pharborist\StatementNode'));
  }

  public function getRoot() {
    return $this->closest(Filter::isInstanceOf('\Pharborist\RootNode'));
  }

  public function hasRoot() {
    return $this->getRoot() !== NULL;

  }

  /**
   * @return TokenNode
   */
  public function previousToken() {
    $prev_node = $this->previous;
    if ($prev_node === NULL) {
      $parent = $this->parent;
      while ($parent !== NULL && $parent->previous === NULL) {
        $parent = $parent->parent;
      }
      if ($parent === NULL) {
        return NULL;
      }
      $prev_node = $parent->previous;
    }
    if ($prev_node instanceof ParentNode) {
      return $prev_node->isEmpty() ? $prev_node->previousToken() : $prev_node->lastToken();
    }
    else {
      return $prev_node;
    }
  }

  /**
   * @return TokenNode
   */
  public function nextToken() {
    $next_node = $this->next;
    if ($next_node === NULL) {
      $parent = $this->parent;
      while ($parent !== NULL && $parent->next === NULL) {
        $parent = $parent->parent;
      }
      if ($parent === NULL) {
        return NULL;
      }
      $next_node = $parent->next;
    }
    if ($next_node instanceof ParentNode) {
      return $next_node->isEmpty() ? $next_node->nextToken() : $next_node->firstToken();
    }
    else {
      return $next_node;
    }
  }

  public function __clone() {
    // Clone does not belong to any parent.
    $this->parent = NULL;
    $this->previous = NULL;
    $this->next = NULL;
  }
}
