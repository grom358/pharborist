<?php

namespace Pharborist\Filters;

use Pharborist\Node;

interface Filter {

  /**
   * Test if a node matches this filter, as configured.
   *
   * @return boolean
   */
  public function __invoke(Node $node);

}
