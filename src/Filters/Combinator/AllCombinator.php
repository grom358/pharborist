<?php

namespace Pharborist\Filters\Combinator;

use Pharborist\NodeInterface;

final class AllCombinator extends CombinatorBase {

  public function __invoke(NodeInterface $node) {
    foreach ($this->callbacks as $callback) {
      $result = $callback($node);

      if (empty($result)) {
        return FALSE;
      }
    }
    return TRUE;
  }

}
