<?php
namespace Pharborist;

/**
 * A function declaration.
 * @package Pharborist
 */
class FunctionDeclaration extends Node {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $parameters;

  /**
   * @var Node
   */
  public $body;
}
