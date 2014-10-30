<?php

namespace Pharborist\Filters;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Node;

class FunctionDeclarationFilter extends FilterBase {

  public function __invoke(Node $node) {
    if ($node instanceof FunctionDeclarationNode) {
      return TRUE;
    }
    return FALSE;
  }

}
