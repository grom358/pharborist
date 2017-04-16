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
   * Adds several filters to this combinator.
   *
   * @param callable[] $filters
   *
   * @return $this
   */
  public function addMultiple(array $filters) {
    array_walk($filters, [ $this, 'add' ]);
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

  /**
   * Removes several filters from this combinator.
   *
   * @param callable[] $filters
   *
   * @return $this
   */
  public function dropMultiple(array $filters) {
    array_walk($filters, [ $this, 'drop' ]);
    return $this;
  }

}
