<?php

namespace Pharborist\Filters;

class FunctionFilter extends FilterBase {

  use NameFilterTrait;

  public function __construct(Node $origin = NULL) {
    parent::__construct($origin);
    $this->nodeTypes[] = '\Pharborist\Functions\FunctionDeclarationNode';
  }

}
