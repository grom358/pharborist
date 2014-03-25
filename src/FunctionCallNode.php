<?php
namespace Pharborist;

/**
 * A function call.
 */
class FunctionCallNode extends ParentNode {
  /**
   * @var Node
   */
  public $functionReference;

  /**
   * @var Node[]
   */
  public $arguments = array();
}
