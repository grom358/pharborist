<?php

namespace Pharborist\Filters;

use Pharborist\Node;

interface FilterFactoryInterface {

  /**
   * Creates a filter for the given origin node.
   *
   * @return FilterInterface
   */
  public static function createFilter(Node $origin);

}
