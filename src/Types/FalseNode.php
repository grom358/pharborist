<?php
namespace Pharborist\Types;

use Pharborist\FormatterFactory;
use Pharborist\Namespaces\NameNode;

/**
 * Boolean FALSE.
 *
 * Represents the boolean FALSE constant, spelled either `FALSE` or `false`. This
 * does *not* represent other falsy values, like empty strings or 0.
 */
class FalseNode extends BooleanNode {
  /**
   * Creates a new FalseNode.
   *
   * @param boolean $boolean
   *   Parameter is ignored.
   *
   * @return FalseNode
   */
  public static function create($boolean = FALSE) {
    $is_upper = FormatterFactory::getDefaultFormatter()->getConfig('boolean_null_upper');
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
