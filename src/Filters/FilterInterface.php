<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;

/**
 * Defines a configurable filter.
 */
interface FilterInterface {

  /**
   * Tests a single node against this filter.
   *
   * @param \Pharborist\NodeInterface $node
   *
   * @return boolean
   */
  public function __invoke(NodeInterface $node);

}
