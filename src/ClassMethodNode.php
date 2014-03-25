<?php
namespace Pharborist;

/**
 * A class method.
 */
class ClassMethodNode extends ParentNode {
  /**
   * @var ModifiersNode
   */
  public $modifiers;

  /**
   * @var Node
   */
  public $reference;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var ParameterNode[]
   */
  public $parameters = array();

  /**
   * @var Node
   */
  public $body;
}
