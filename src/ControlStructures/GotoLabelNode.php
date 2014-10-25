<?php
namespace Pharborist\ControlStructures;

use Pharborist\ParentNode;
use Pharborist\TokenNode;

/**
 * A goto label.
 */
class GotoLabelNode extends ParentNode {
  /**
   * @var TokenNode
   */
  protected $label;

  /**
   * @return TokenNode
   */
  public function getLabel() {
    return $this->label;
  }
}
