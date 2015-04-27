<?php

namespace Pharborist\Filters;

use Pharborist\Functions\FunctionCallNode;
use Pharborist\NodeInterface;

class FunctionCallFilter implements FilterInterface {

  /**
   * @var string[]
   */
  protected $functions = [];

  public function __construct(array $functions) {
    $this->functions = $functions;
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    return ($node instanceof FunctionCallNode && in_array($node->getName()->getText(), $this->functions));
  }

}
