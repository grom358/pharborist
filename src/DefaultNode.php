<?php
namespace Pharborist;

/**
 * A default statement in switch control structure.
 */
class DefaultNode extends StatementNode {
  protected $properties = array(
    'body' => NULL,
  );

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
