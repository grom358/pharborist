<?php
namespace Pharborist;

/**
 * A default statement in switch control structure.
 */
class DefaultNode extends ParentNode {
  protected $properties = array(
    'body' => NULL,
  );

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
