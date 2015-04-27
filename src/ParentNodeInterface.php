<?php
namespace Pharborist;

interface ParentNodeInterface extends NodeInterface {
  /**
   * Get the number of children.
   * @return int
   */
  public function childCount();

  /**
   * Return the first child.
   * @return Node
   */
  public function firstChild();

  /**
   * Return the last child.
   * @return Node
   */
  public function lastChild();

  /**
   * Get the immediate children of this node.
   * @param callable $callback An optional callback to filter by.
   * @return NodeCollection
   */
  public function children(callable $callback = NULL);

  /**
   * Remove all child nodes.
   */
  public function clear();

  /**
   * Prepend nodes to this node.
   * @param Node|Node[]|NodeCollection $nodes
   * @return $this
   * @throws \InvalidArgumentException
   */
  public function prepend($nodes);

  /**
   * Append nodes to this node.
   * @param Node|Node[]|NodeCollection $nodes
   * @return $this
   * @throws \InvalidArgumentException
   */
  public function append($nodes);

  /**
   * Get the first (i.e. leftmost leaf) token.
   * @return TokenNode
   */
  public function firstToken();

  /**
   * Get the last (i.e. rightmost leaf) token.
   * @return TokenNode
   */
  public function lastToken();

  /**
   * Test if the node has a descendant that matches.
   * @param callable $callback Callback to test for match.
   * @return NodeCollection
   */
  public function has(callable $callback);

  /**
   * Test if the node is a descendant of this node.
   * @param Node $node Node to test
   * @return boolean
   */
  public function isDescendant(Node $node);

  /**
   * Find descendants that pass filter callback.
   * @param callable $callback Callback to filter by.
   * @return NodeCollection
   */
  public function find(callable $callback);

  /**
   * Perform callback on this node and all descendant nodes.
   * @param callable $callback Callback for each node.
   */
  public function walk(callable $callback);
}
