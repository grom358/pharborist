<?php
namespace Pharborist\Functions;

use Pharborist\NodeCollection;

class ParameterNodeCollection extends NodeCollection {
  /**
   * Implements \ArrayAccess::offsetExists().
   *
   * @param integer $offset
   *
   * @return boolean
   */
  public function offsetExists($offset) {
    if (is_string($offset)) {
      // To deal with php allowing function test($a, $a) loop in reverse.
      foreach (array_reverse($this->nodes) as $node) {
        if ($node instanceof ParameterNode) {
          if ($node->getName() === $offset) {
            return TRUE;
          }
        }
      }
      return FALSE;
    }
    return isset($this->nodes[$offset]);
  }

  /**
   * Implements \ArrayAccess::offsetGet().
   *
   * @param integer $offset
   *
   * @return ParameterNode
   */
  public function offsetGet($offset) {
    if (is_string($offset)) {
      // To deal with php allowing function test($a, $a) loop in reverse.
      foreach (array_reverse($this->nodes) as $node) {
        if ($node instanceof ParameterNode) {
          if ($node->getName() === $offset) {
            return $node;
          }
        }
      }
      return NULL;
    }
    return parent::offsetGet($offset);
  }
}
