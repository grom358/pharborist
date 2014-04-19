<?php
namespace Pharborist;

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
