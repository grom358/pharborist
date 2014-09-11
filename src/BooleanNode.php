<?php
namespace Pharborist;

/**
 * Base class for TRUE and FALSE.
 */
abstract class BooleanNode extends ConstantNode {
  /**
   * Create
   *
   * @param mixed $boolean
   *   Boolean value.
   *
   * @return BooleanNode
   */
  public static function create($boolean) {
    $is_upper = Settings::get('formatter.boolean_null.upper', TRUE);
    if ($boolean) {
      return ConstantNode::create($is_upper ? 'TRUE' : 'true');
    }
    else {
      return ConstantNode::create($is_upper ? 'FALSE' : 'false');
    }
  }

  /**
   * Boolean value of constant.
   *
   * @return boolean
   *   TRUE or FALSE.
   */
  abstract public function toBoolean();
}
