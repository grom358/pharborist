<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;

class NodeTypeFilter implements FilterInterface {

  /**
   * @var string[]
   */
  protected $nodeTypes = [];

  /**
   * If TRUE, the configured node types will NOT pass the filter.
   *
   * @var boolean
   */
  protected $not = FALSE;

  public function __construct(array $node_types) {
    $this->nodeTypes = array_map(function ($type) {
      return ltrim($type, '\\');
    }, $node_types);
  }

  /**
   * Negates the filter.
   *
   * @return $this
   */
  public function not() {
    $this->not = TRUE;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    $result = FALSE;
    // Don't use in_array(), because it will not account for inheritance.
    foreach ($this->nodeTypes as $node_type) {
      if ($node instanceof $node_type) {
        $result = TRUE;
        break;
      }
    }
    return ($this->not ? empty($result) : $result);
  }

}
