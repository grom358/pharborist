<?php
namespace Pharborist;

/**
 * A function declaration.
 * @package Pharborist
 */
class FunctionDeclarationNode extends StatementNode {
  /**
   * @var Node
   */
  public $reference;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var ParameterListNode
   */
  public $parameters;

  /**
   * @var Node
   */
  public $body;
}
