<?php

namespace Pharborist\Filters;

final class AnyCombinator extends CombinatorBase {

  public function __invoke(Node $node) {
    foreach ($this->callbacks as $callback) {
      if ($callback($node)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
