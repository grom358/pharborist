<?php
namespace Pharborist;

/**
 * Method/member modifiers.
 */
class ModifiersNode extends ParentNode {
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
