<?php
namespace Pharborist;

/**
 * An interface method.
 */
class InterfaceMethodNode extends ParentNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var Node
   */
  public $visibility;

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
}
