<?php

namespace Pharborist\Filters;

use Pharborist\Node;

/**
 * Defines a configurable filter.
 */
interface FilterInterface {

  public function __invoke(Node $node);

  public function isMatch();

  public function hasMatch();

  public function children();

  public function find();

  public function parentIsMatch();

  public function parents();

  public function siblings();

  public function previousIsMatch();

  public function previousAll();

  public function previousUntil(callable $until, $inclusive = TRUE);

  public function nextIsMatch();

  public function nextAll();

  public function nextUntil(callable $until, $inclusive = TRUE);

}
