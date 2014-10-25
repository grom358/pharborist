<?php
namespace Pharborist\Types;

use Pharborist\Constants\ConstantNode;
use Pharborist\Namespaces\NameNode;
use Pharborist\Settings;

/**
 * The NULL constant, spelled `null` or `NULL`.
 */
class NullNode extends ConstantNode implements ScalarNode {
  /**
   * Create a new NullNode.
   *
   * @param string $name
   *   Parameter is ignored.
   *
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
  public function toValue() {
    return NULL;
  }
}
