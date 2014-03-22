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
   * @var ArgumentListNode
   */
  public $arguments;
}
