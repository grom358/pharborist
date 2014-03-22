<?php
namespace Pharborist;

/**
 * A new expression. Eg. new MyClass()
 * @package Pharborist
 */
class NewNode extends Node {
  /**
   * @var NamespacePathNode
   */
  public $className;

  /**
   * @var Node[]
   */
  public $arguments = array();
}
