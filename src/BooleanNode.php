<?php
namespace Pharborist;

/**
 * Base class for TRUE and FALSE.
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
  abstract public function toBoolean();

  /**
   * @return boolean
   */
  public function getValue() {
    return $this->toBoolean();
  }
}
