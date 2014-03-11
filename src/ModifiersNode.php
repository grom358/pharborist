<?php
namespace Pharborist;

/**
 * Method/member modifiers.
 * @package Pharborist
 */
class ModifiersNode extends Node {
  /**
   * @var Node
   */
  public $abstract;

  /**
   * @var Node
   */
  public $final;

  /**
   * @var Node
   */
  public $static;

  /**
   * @var Node
   */
  public $visibility;
}
