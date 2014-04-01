<?php
namespace Pharborist;

/**
 * A function declaration.
 */
class FunctionDeclarationNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

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
