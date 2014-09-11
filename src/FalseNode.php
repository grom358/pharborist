<?php
namespace Pharborist;

/**
 * FALSE boolean.
 */
class FalseNode extends BooleanNode {
  /**
   * @return FalseNode
   */
  public static function create($boolean = FALSE) {
    return BooleanNode::create(FALSE);
  }

  public function toBoolean() {
    return FALSE;
  }
}
