<?php

namespace Pharborist\Filters\Combinator;

abstract class CombinatorBase implements CombinatorInterface {

  /**
   * @var callable[]
   */
  protected $callbacks = [];

  /**
   * {@inheritdoc}
   */
  public function has(callable $filter) {
    return in_array($filter, $this->callbacks, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function add(callable $filter) {
    if (! $this->has($filter)) {
      $this->callbacks[] = $filter;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function drop(callable $filter) {
    $index = array_search($filter, $this->callbacks, TRUE);
    if (is_integer($index)) {
      unset($this->callbacks[$index]);
    }
    return $this;
  }

}
