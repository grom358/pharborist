<?php
namespace Pharborist\Types;

use Pharborist\FormatterFactory;
use Pharborist\Namespaces\NameNode;

/**
 * Boolean TRUE.
 *
 * Represents the boolean TRUE constant, spelled `true` or `TRUE`. This does *not* represent
 * other truthy values like 1 or `'hello'`.
 */
class TrueNode extends BooleanNode {
  /**
   * Create a new TrueNode.
   *
   * @param boolean $boolean
   *   Parameter is ignored.
   *
   * @return TrueNode
   */
  public static function create($boolean = TRUE) {
    $is_upper = FormatterFactory::getDefaultFormatter()->getConfig('boolean_null_upper');
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
