<?php
namespace Pharborist;

/**
 * A goto label.
 */
class GotoLabelNode extends ParentNode {
  protected $properties = array(
    'label' => NULL,
  );

  /**
   * @var Node
   */
  public function getLabel() {
    return $this->properties['label'];
  }
}
