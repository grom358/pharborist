<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;
use Pharborist\WhitespaceNode;

class NewLineFilter extends ExecutableFilter{

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    return (
      $node instanceof WhitespaceNode
      &&
      $node->getNewlineCount() > 0
    );
  }

}
