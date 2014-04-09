<?php
namespace Pharborist;

/**
 * A case statement in switch control structure.
 */
class CaseNode extends ParentNode {
  protected $properties = array(
    'matchOn' => NULL,
    'body' => NULL,
  );

  /**
   * @return Node
   */
  public function getMatchOn() {
    return $this->properties['matchOn'];
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
