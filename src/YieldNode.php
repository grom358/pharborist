<?php
namespace Pharborist;

/**
 * A yield expression.
 */
class YieldNode extends ParentNode {
  /**
   * @var Node
   */
  public $key;

  /**
   * @var Node
   */
  public $value;
}
