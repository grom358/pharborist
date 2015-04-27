<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;

/**
 * Inverse of ExecutableFilter; only passes white space and comments.
 */
class HiddenFilter extends ExecutableFilter {

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    return (! parent::__invoke($node));
  }

}
