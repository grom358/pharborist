<?php
namespace Pharborist;

/**
 * Class AnonymousFunctionNode
 * @package Pharborist
 */
class AnonymousFunctionNode extends Node {
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
