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
   * @var ParameterListNode
   */
  public $parameters;

  /**
   * @var (VariableNode|ReferenceVariableNode)[]
   */
  public $lexicalVariables = array();

  /**
   * @var Node
   */
  public $body;
}
