<?php
namespace Pharborist;

/**
 * FALSE boolean.
 */
class FalseNode extends BooleanNode {
  public function toBoolean() {
    return FALSE;
  }
}
