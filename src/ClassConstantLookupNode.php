<?php
namespace Pharborist;

/**
 * A class constant lookup. Eg. MyClass::MY_CONST
 * @package Pharborist
 */
class ClassConstantLookupNode extends Node {
  /**
   * @var Node
   */
  public $className;

  /**
   * @var Node
   */
  public $constantName;
}
