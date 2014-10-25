<?php
namespace Pharborist;

use Pharborist\Constants\ConstantNode;
use Pharborist\Namespaces\NameNode;

/**
 * The NULL constant, spelled `null` or `NULL`.
 */
class NullNode extends ConstantNode implements ScalarNode {
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
  public function toValue() {
    return NULL;
  }
}
