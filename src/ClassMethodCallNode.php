<?php
namespace Pharborist;

/**
 * A lookup to a class method. Eg. MyClass::classMethod
 * @package Pharborist
 */
class ClassMethodCallNode extends Node {
  /**
   * @var Node
   */
  public $className;

  /**
   * @var Node
   */
  public $methodName;

  /**
   * @var Node[]
   */
  public $arguments = array();
}
