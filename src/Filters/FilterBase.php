<?php

namespace Pharborist\Filters;

use Pharborist\Node;

abstract class FilterBase implements Filter {

  /**
   * @var \Pharborist\Node
   */
  protected $origin;

  public function __construct(Node $origin = NULL) {
    $this->origin = $origin;
  }

}
