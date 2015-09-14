<?php
namespace Pharborist;

/**
 * Public API for Node.
 *
 * This interface is used by tagging interfaces such as ExpressionNode to
 * indicate they support the Node API.
 */
interface NodeInterface {
  /**
   * Get the file name for this node.
   * @return string
   */
  public function getFilename();

  /**
   * Get the line number of the node.
   * @return int
   */
  public function getLineNumber();

  /**
   * Get the number of newlines for this node.
   * @return int
   */
  public function getNewlineCount();

  /**
   * Get the column number of the node.
   * @return int
   */
  public function getColumnNumber();

  /**
   * Get the byte offset to this node.
   * @return int
   */
  public function getByteOffset();

  /**
   * Get the byte length of this node.
   * @return int
   */
  public function getByteLength();

  /**
   * Convert the node into PHP source code.
   * @return string
   */
  public function getText();

  /**
   * Get the parent node.
   * @param callable $callback An optional callback to filter by.
   * @return ParentNode
   */
  public function parent(callable $callback = NULL);

  /**
   * Get the ancestors of this node.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function parents(callable $callback = NULL);

  /**
   * Get ancestors up to the node matched by callback.
   * @param callable $callback Callback to test for match.
   * @param bool $inclusive TRUE to include the node matched by callback.
   * @return NodeCollection
   */
  public function parentsUntil(callable $callback, $inclusive = FALSE);

  /**
   * Get the first node matched by the callback by testing this node and
   * traversing up through its ancestors in the tree.
   * @param callable $callback Callback to test for match.
   * @return Node
   */
  public function closest(callable $callback);

  /**
   * Get the last node matched by the callback by testing this node and
   * traversing up through its ancestors in the tree.
   * @param callable $callback Callback to test for match.
   * @return Node
   */
  public function furthest(callable $callback);

  /**
   * Get the position of the element relative to its sibling elements.
   * @return int
   */
  public function index();

  /**
   * Get the previous sibling.
   * @param callable $callback An optional callback to filter by.
   * @return Node
   */
  public function previous(callable $callback = NULL);

  /**
   * Get the previous siblings of this node.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function previousAll(callable $callback = NULL);

  /**
   * Get all the preceding siblings up to but not including the match.
   * @param callable $callback Callback to test for match.
   * @param bool $inclusive TRUE to include the node matched by callback.
   * @return NodeCollection
   */
  public function previousUntil(callable $callback, $inclusive = FALSE);

  /**
   * Get the next immediate sibling.
   * @param callable $callback An optional callback to filter by.
   * @return Node
   */
  public function next(callable $callback = NULL);

  /**
   * Get all the following siblings.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function nextAll(callable $callback = NULL);

  /**
   * Get all the following siblings up to but not including the match.
   * @param callable $callback Callback to test for match.
   * @param bool $inclusive TRUE to include the node matched by callback.
   * @return NodeCollection
   */
  public function nextUntil(callable $callback, $inclusive = FALSE);

  /**
   * Get the siblings.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function siblings(callable $callback = NULL);

  /**
   * Insert this node before targets.
   * @param Node|Node[]|NodeCollection $targets Nodes to insert before.
   * @return $this
   */
  public function insertBefore($targets);

  /**
   * Insert nodes before this node.
   * @param Node|Node[]|NodeCollection $nodes Nodes to insert.
   * @return $this
   */
  public function before($nodes);

  /**
   * Insert this node after targets.
   * @param Node|Node[]|NodeCollection $targets Nodes to insert after.
   * @return $this
   */
  public function insertAfter($targets);

  /**
   * Insert nodes after this node.
   * @param Node|Node[]|NodeCollection $nodes Nodes to insert.
   * @return $this
   */
  public function after($nodes);

  /**
   * Remove this node from its parent.
   * @return $this
   */
  public function remove();

  /**
   * Replace this node with another node.
   * @param Node|Node[]|NodeCollection|callable $node Replacement node.
   * @return $this
   */
  public function replaceWith($node);

  /**
   * Swap this node with another.
   * @param Node $node Node to swap with.
   * @return $this
   */
  public function swapWith(Node $node);

  /**
   * Replace nodes with this node.
   * @param Node|Node[]|NodeCollection $targets Nodes to replace.
   * @return $this
   */
  public function replaceAll($targets);

  /**
   * Prepend this node to target.
   * @param ParentNode|ParentNode[]|NodeCollection $targets Targets to prepend to.
   * @return $this
   */
  public function prependTo($targets);

  /**
   * Append this node to target.
   * @param ParentNode|ParentNode[]|NodeCollection $targets Targets to append to.
   * @return $this
   */
  public function appendTo($targets);

  /**
   * Returns the root node if this node belongs to one.
   *
   * @return RootNode|NULL
   */
  public function getRoot();

  /**
   * Returns TRUE if this node belongs to a root node.
   */
  public function hasRoot();
}
