<?php

namespace Pharborist\Filters\Combinator;

use Pharborist\NodeInterface;

final class AnyCombinator extends CombinatorBase {

  public function __invoke(NodeInterface $node) {
    foreach ($this->callbacks as $callback) {
      if ($callback($node)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
