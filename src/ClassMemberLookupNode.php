<?php
namespace Pharborist;

/**
 * A class member lookup. Eg. MyClass::$a
 * @package Pharborist
 */
class ClassMemberLookupNode extends Node {
  /**
   * @var Node
   */
  public $className;

  /**
   * @var Node
   */
  public $memberName;
}
