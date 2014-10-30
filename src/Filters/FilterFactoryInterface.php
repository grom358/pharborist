<?php

namespace Pharborist\Filters;

use Pharborist\Node;

interface FilterFactoryInterface {

  /**
   * @return Filter
   */
  public static function createFilter(Node $origin);

}
