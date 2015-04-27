<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;

class NodeTypeFilter implements FilterInterface {

  /**
   * @var string[]
   */
  protected $nodeTypes = [];

  public function __construct(array $node_types) {
    $this->nodeTypes = array_map(function ($type) {
      return ltrim($type, '\\');
    }, $node_types);
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    // Don't use in_array(), because it will not account for inheritance.
    foreach ($this->nodeTypes as $node_type) {
      if ($node instanceof $node_type) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
