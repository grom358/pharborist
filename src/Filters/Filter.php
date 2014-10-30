<?php

namespace Pharborist\Filters;

use Pharborist\Node;

interface Filter {

  /**
   * Returns if the origin matches this filter.
   *
   * @return boolean
   *
   * @throws \BadMethodCallException if there is no origin.
   */
  public function isMatch();

  /**
   * Returns if the origin has any descendants which match this filter.
   *
   * @return boolean
   *
   * @throws \BadMethodCallException if there is no origin, or if it doesn't
   * implement ParentNodeInterface.
   */
  public function hasMatch();

  /**
   * Returns descendants of the origin which match this filter.
   *
   * @return \Pharborist\NodeCollection
   *
   * @throws \BadMethodCallException if there is no origin, or if it doesn't
   * implement ParentNodeInterface.
   */
  public function find();

  /**
   * Returns children of the origin which match this filter.
   *
   * @return \Pharborist\NodeCollection
   *
   * @throws \BadMethodCallException if there is no origin, or if it doesn't
   * implement ParentNodeInterface.
   */
  public function matchChildren();

  /**
   * Returns if the origin's parent matches this filter.
   *
   * @return boolean
   *
   * @throws \BadMethodCallException if there is no origin.
   */
  public function matchParent();

  /**
   * Returns parents of the origin which match this filter.
   *
   * @return \Pharborist\NodeCollection
   *
   * @throws \BadMethodCallException if there is no origin, or if it doesn't
   * implement ParentNodeInterface.
   */
  public function matchParents();

  /**
   * Returns siblings of the origin which match this filter.
   *
   * @return \Pharborist\NodeCollection
   *
   * @throws \BadMethodCallException if there is no origin, or if it doesn't
   * implement ParentNodeInterface.
   */
  public function matchSiblings();

  /**
   * Returns if the origin's previous sibling matches this filter.
   *
   * @return boolean
   *
   * @throws \BadMethodCallException if there is no origin.
   */
  public function matchPrevious();

  /**
   * Returns all of the origin's previous siblings until the first one that
   * matches the $until predicate, filtered by this filter.
   *
   * @return \Pharborist\NodeCollection
   *
   * @throws \BadMethodCallException if there is no origin.
   */
  public function matchPreviousUntil(callable $until, $inclusive = FALSE);

  /**
   * Returns all of the origin's previous siblings that match this filter.
   *
   * @return \Pharborist\NodeCollection
   *
   * @throws \BadMethodCallException if there is no origin.
   */
  public function matchPreviousAll();

  public function matchNext();

  public function matchNextUntil(callable $until, $inclusive = FALSE);

  public function matchNextAll();

  /**
   * Test if a node matches this filter, as configured.
   *
   * @return boolean
   */
  public function __invoke(Node $node);

}
