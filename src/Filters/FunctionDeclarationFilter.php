<?php

namespace Pharborist\Filters;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\NodeInterface;

class FunctionDeclarationFilter implements FilterInterface {

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
    return $node instanceof FunctionDeclarationNode && in_array($node->getName()->getText(), $this->functions);
  }

}
