<?php
namespace Pharborist;

/**
 * A catch in a try control structure.
 */
class CatchNode extends ParentNode {
  /**
   * @var NamespacePathNode
   */
  public $exceptionType;

  /**
   * @var VariableNode
   */
  public $variable;

  /**
   * @var Node
   */
  public $body;
}
