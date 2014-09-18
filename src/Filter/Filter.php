<?php
namespace Pharborist\Filter;

use Pharborist\Node;

interface FilterInterface {
  /**
   * @return boolean
   */
  public function __invoke(Node $node);

  /**
   * @return $this
   */
  public function chain(callable $filter);
}
