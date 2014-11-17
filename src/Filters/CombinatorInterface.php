<?php

namespace Pharborist\Filters;

use Pharborist\Node;

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
   * Executes all added filters to the given node.
   *
   * @return boolean
   */
  public function __invoke(Node $node);

}
