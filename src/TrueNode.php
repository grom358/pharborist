<?php
namespace Pharborist;

/**
 * TRUE boolean.
 */
class TrueNode extends BooleanNode {
  public function toBoolean() {
    return TRUE;
  }
}
