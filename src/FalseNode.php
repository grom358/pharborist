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
    $is_upper = Settings::get('formatter.boolean_null.upper', TRUE);
    $node = new FalseNode();
    $node->addChild(NameNode::create($is_upper ? 'FALSE' : 'false'), 'constantName');
    return $node;
  }

  /**
   * Gets the boolean value of the node.
   *
   * @return boolean
   */
  public function toValue() {
    return FALSE;
  }
}
