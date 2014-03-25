<?php
namespace Pharborist;

/**
 * A function parameter.
 */
class ParameterNode extends ParentNode {
  /**
   * @var Node
   */
  public $classType;

  /**
   * @var Node
   */
  public $reference;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $defaultValue;
}
