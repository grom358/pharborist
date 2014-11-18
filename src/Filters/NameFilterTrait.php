<?php

namespace Pharborist\Filters;

use Pharborist\Node;

/**
 * Trait for filters which can filter on a node name (getName()).
 */
trait NameFilterTrait {

  /**
   * @var string[]
   */
  protected $names = [];

  public function name($name) {
    if (isset($name)) {
      if (empty($this->callbacks['name'])) {
        $this->callbacks['name'] = function(Node $node) {
          return in_array($node->getName()->getText(), $this->names);
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
