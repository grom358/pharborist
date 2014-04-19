<?php
namespace Pharborist;

/**
 * A default statement in switch control structure.
 */
class DefaultNode extends StatementNode {
  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
