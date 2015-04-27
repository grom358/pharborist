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
    return in_array(get_class($node), $this->nodeTypes);
  }

}
