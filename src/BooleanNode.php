<?php
namespace Pharborist;

/**
 * Base class for TRUE and FALSE.
 */
abstract class BooleanNode extends ConstantNode {
  /**
   * Boolean value of constant.
   *
   * @return boolean
   *   TRUE or FALSE.
   */
  abstract public function toBoolean();
}
