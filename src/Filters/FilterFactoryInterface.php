<?php

namespace Pharborist\Filters;

use Pharborist\Node;

interface FilterFactoryInterface {

  /**
   * Creates a filter, with the origin set to the node on which the method
   * was called.
   *
   * @return FilterInterface
   */
  public function createFilter();

}
