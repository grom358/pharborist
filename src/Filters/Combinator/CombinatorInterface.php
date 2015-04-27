<?php

namespace Pharborist\Filters\Combinator;

use Pharborist\NodeInterface;

/**
 * Defines a strategy for aggregating the result of several executable filters.
 */
interface CombinatorInterface {
  
  /**
   * Returns if a given filter is in the combinator.
   *
   * @return boolean
   */
  public function has(callable $filter);

  /**
   * Adds a filter to the combinator.
   *
   * @return self
   */
  public function add(callable $filter);
  
  /**
   * Removes a specific filter from the combinator.
   *
   * @return self
   */
  public function drop(callable $filter);

  /**
   * Executes all added filters to the given node.
   *
   * @param \Pharborist\NodeInterface $node
   *  The node to test against the filters.
   *
   * @return boolean
   */
  public function __invoke(NodeInterface $node);

}
