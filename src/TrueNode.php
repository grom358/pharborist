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
    $is_upper = Settings::get('formatter.boolean_null.upper', TRUE);
    $node = new TrueNode();
    $node->addChild(NameNode::create($is_upper ? 'TRUE' : 'true'), 'constantName');
    return $node;
  }

  public function toBoolean() {
    return TRUE;
  }
}
