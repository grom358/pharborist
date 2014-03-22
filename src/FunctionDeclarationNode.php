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
   * @var ParameterNode[]
   */
  public $parameters = array();

  /**
   * @var Node
   */
  public $body;
}
