<?php
namespace Pharborist;

/**
 * TRUE boolean.
 */
class TrueNode extends BooleanNode {
  /**
   * @return TrueNode
   */
  public static function create($boolean = TRUE) {
    return BooleanNode::create(TRUE);
  }

  public function toBoolean() {
    return TRUE;
  }
}
