<?php
namespace Pharborist;

/**
 * A node in the PHP syntax tree.
 */
abstract class Node {
  /**
   * @var Node
   */
  public $parent = NULL;

  abstract public function getSourcePosition();
}
