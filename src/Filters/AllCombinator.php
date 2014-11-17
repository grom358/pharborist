<?php

namespace Pharborist\Filters;

final class AllCombinator extends CombinatorBase {

  public function __invoke(Node $node) {
    foreach ($this->callbacks as $callback) {
      $result = $callback($node);

      if (empty($result)) {
        return FALSE;
      }
    }
    return TRUE;
  }

}
