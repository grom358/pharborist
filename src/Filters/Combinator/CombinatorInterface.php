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
   * @return $this
   */
  public function add(callable $filter);
  
  /**
   * Removes a specific filter from the combinator.
   *
   * @return $this
   */
  public function drop(callable $filter);

  /**
   * Tests the given node against all filters in the combinator.
   *
   * @return boolean
   */
  public function __invoke(NodeInterface $node);

}
