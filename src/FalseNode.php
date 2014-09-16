<?php
namespace Pharborist;

/**
 * Boolean FALSE.
 *
 * <p>Represents the boolean FALSE constant, spelled either <code>FALSE</code>
 * or <code>false</code>. This does <em>not</em> represent other falsy values like
 * '' or 0.</p>
 */
class FalseNode extends BooleanNode {
  /**
   * Creates a new FalseNode.
   *
   * @return FalseNode
   */
  public static function create($boolean = FALSE) {
    return BooleanNode::create(FALSE);
  }

  /**
   * Gets the boolean value of the node.
   *
   * @return boolean
   */
  public function toBoolean() {
    return FALSE;
  }
}
