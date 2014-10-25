<?php
namespace Pharborist\Types;

use Pharborist\Namespaces\NameNode;
use Pharborist\Settings;

/**
 * Boolean TRUE.
 *
 * Represents the boolean TRUE constant, spelled `true` or `TRUE`. This does *not* represent
 * other truthy values like 1 or `'hello'`.
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

  /**
   * Gets the boolean value of the node.
   *
   * @return boolean
   */
  public function toValue() {
    return TRUE;
  }
}
