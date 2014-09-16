<?php
namespace Pharborist;

/**
 * The constant NULL.
 */
class NullNode extends ConstantNode {
  /**
   * @return NullNode
   */
  public static function create($name = 'null') {
    $is_upper = Settings::get('formatter.boolean_null.upper', TRUE);
    $node = new NullNode();
    $node->addChild(NameNode::create($is_upper ? 'NULL' : 'null'), 'constantName');
    return $node;
  }

  /**
   * @return null
   */
  public function getValue() {
    return NULL;
  }
}
