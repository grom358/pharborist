<?php
namespace Pharborist;

/**
 * A function declaration.
 * @package Pharborist
 */
class FunctionDeclarationNode extends Node {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var ListNode
   */
  public $parameters;

  /**
   * @var Node
   */
  public $body;
}
