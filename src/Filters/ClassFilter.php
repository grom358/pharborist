<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;
use Pharborist\Objects\ClassNode;

class ClassFilter implements FilterInterface {

  /**
   * @var string[]
   */
  protected $classes = [];

  public function __construct(array $classes) {
    $this->classes = $classes;
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    return ($node instanceof ClassNode && in_array($node->getName()->getText(), $this->classes));
  }

}
