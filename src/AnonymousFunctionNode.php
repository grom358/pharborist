<?php
namespace Pharborist;

/**
 * An anonymous function.
 */
class AnonymousFunctionNode extends ParentNode implements ExpressionNode {
  /**
   * @var Node
   */
  public $reference;

  /**
   * @var ParameterNode[]
   */
  public $parameters = array();

  /**
   * @var (VariableNode|ReferenceVariableNode)[]
   */
  public $lexicalVariables = array();

  /**
   * @var Node
   */
  public $body;
}
