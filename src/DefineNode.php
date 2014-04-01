<?php
namespace Pharborist;

/**
 * A function call to define().
 *
 * Define creates global level constants, therefore this class exists to allow
 * special treatment the function call.
 */
class DefineNode extends FunctionCallNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;
}
