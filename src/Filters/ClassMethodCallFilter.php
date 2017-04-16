<?php

namespace Pharborist\Filters;

use Pharborist\NodeInterface;
use Pharborist\Objects\ClassMethodCallNode;

class ClassMethodCallFilter implements FilterInterface {

  /**
   * @var string
   */
  protected $class;

  /**
   * @var string
   */
  protected $method;

  public function __construct($class, $method) {
    $this->class = $class;
    $this->method = $method;
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    return (
      $node instanceof ClassMethodCallNode
      &&
      $node->getClassName()->getText() == $this->class
      &&
      $node->getMethodName()->getText() == $this->method
    );
  }

}
