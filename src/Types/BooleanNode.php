<?php
namespace Pharborist\Types;

use Pharborist\Constants\ConstantNode;

/**
 * Base class for TRUE and FALSE.
 *
 * @see TrueNode
 * @see FalseNode
 */
abstract class BooleanNode extends ConstantNode implements ScalarNode {
  /**
   * Creates a BooleanNode.
   *
   * @param mixed $boolean
   *  The boolean to create. Pass a truthy value for TrueNode, falsy for FalseNode.
   *
   * @return BooleanNode
   */
  public static function create($boolean) {
    if ($boolean) {
      return TrueNode::create();
    }
    else {
      return FalseNode::create();
    }
  }

  /**
   * Returns the boolean value of constant.
   *
   * @return boolean
   */
  abstract public function toValue();
}
