<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;

class SingleNodeFilter implements FilterInterface {

  /**
   * @var string
   */
  protected $nodeType;

  /**
   * @var string[]
   */
  protected $names = [];

  public function __construct($node_type, array $names) {
    $this->nodeType = $node_type;
    $this->names = $names;
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    return is_a($node, $this->nodeType) && in_array($node->getName()->getText(), $this->names);
  }

}
