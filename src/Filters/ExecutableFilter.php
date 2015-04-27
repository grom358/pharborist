<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;

/**
 * Inverse of HiddenFilter. Passes anything that isn't white space or a comment.
 */
class ExecutableFilter extends HiddenFilter {

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    return (! parent::__invoke($node));
  }

}
