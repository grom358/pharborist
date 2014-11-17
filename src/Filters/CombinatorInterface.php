<?php

namespace Pharborist\Filters;

use Pharborist\Node;

interface CombinatorInterface {

  /**
   * Adds a filter to the combinator.
   *
   * @return $this
   */
  public function add(callable $filter);
  
  /**
   * Returns if a given filter is in the combinator.
   *
   * @return boolean
   */
  public function has(callable $filter);
  
  /**
   * Executes all added filters to the given node.
   *
   * @return boolean
   */
  public function __invoke(Node $node);

}
