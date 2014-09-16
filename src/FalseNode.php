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
    $is_upper = Settings::get('formatter.boolean_null.upper', TRUE);
    $node = new FalseNode();
    $node->addChild(NameNode::create($is_upper ? 'FALSE' : 'false'), 'constantName');
    return $node;
  }

  public function toBoolean() {
    return FALSE;
  }
}
