<?php

namespace Pharborist\Filters;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Node;
use Pharborist\TokenNode;

class FunctionFilter extends FilterBase {

  /**
   * @var string[]
   */
  protected $names = [];

  public function __construct(Node $origin = NULL) {
    parent::__construct($origin);
    $this->nodeTypes[] = '\Pharborist\Functions\FunctionDeclarationNode';
  }

  public function name($name) {
    if (isset($name)) {
      if (empty($this->callbacks['name'])) {
        $this->callbacks['name'] = function(FunctionDeclarationNode $function) {
          return in_array($function->getName()->getText(), $this->names);
        };
      }
      $this->names[] = $name;
    }
    else {
      unset($this->callbacks['name']);
      $this->names = [];
    }
  }

}
