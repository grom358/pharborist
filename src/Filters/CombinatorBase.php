<?php

namespace Pharborist\Filters;

use Pharborist\Node;

abstract class CombinatorBase implements CombinatorInterface {

  /**
   * @var callable[]
   */
  protected $callbacks = [];

  public function add(callable $filter) {
    if (! $this->has($filter)) {
      $this->callbacks[] = $filter;
    }
    return $this;
  }

  public function has(callable $filter) {
    // By default, this implementation is NOT differentiating filters by
    // their configuration.
    return in_array($filter, $this->callbacks, TRUE);
  }

}
