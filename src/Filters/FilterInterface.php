<?php

namespace Pharborist\Filters;

use Pharborist\Node;

/**
 * Defines a configurable filter.
 */
interface FilterInterface {

  /**
   * Tests a single node against this filter.
   *
   * @param Node $node
   *
   * @return boolean
   */
  public function __invoke(Node $node);

  /**
   * Returns if the origin matches the filter.
   *
   * @return boolean
   */
  public function isMatch();

  /**
   * Returns if the origin has a descendant which matches the filter.
   *
   * @return boolean
   */
  public function hasMatch();

  /**
   * Returns children of the origin which match the filter.
   *
   * @return \Pharborist\NodeCollection
   */
  public function children();

  /**
   * Returns all descendants of the origin which match the filter.
   *
   * @return \Pharborist\NodeCollection
   */
  public function find();

  /**
   * Returns if the origin's parent matches the filter.
   *
   * @return boolean
   */
  public function parentIsMatch();

  /**
   * Returns parents of the origin which match the filter.
   *
   * @return \Pharborist\NodeCollection
   */
  public function parents();

  /**
   * Returns the nearest parent of the origin which matches the filter.
   *
   * @return \Pharborist\Node|NULL
   */
  public function closest();

  /**
   * Returns the furthest parent of the origin which matches the filter.
   *
   * @return \Pharborist\Node|NULL
   */
  public function furthest();

  /**
   * Returns siblings of the origin which match the filter.
   *
   * @return \Pharborist\NodeCollection
   */
  public function siblings();

  /**
   * Returns if the origin's previous sibling matches the filter.
   *
   * @return boolean
   */
  public function previousIsMatch();

  /**
   * Returns the origin's previous siblings, filtered.
   *
   * @return \Pharborist\NodeCollection
   */
  public function previousAll();

  /**
   * Returns the origin's previous siblings, filtered, stopping at the first
   * one which matches the predicate.
   *
   * @param callable $until
   * @param bool $inclusive
   *
   * @return \Pharborist\NodeCollection
   */
  public function previousUntil(callable $until, $inclusive = TRUE);

  public function nextIsMatch();

  public function nextAll();

  public function nextUntil(callable $until, $inclusive = TRUE);

}
