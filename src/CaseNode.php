<?php
namespace Pharborist;

/**
 * A case statement in switch control structure.
 */
class CaseNode extends StatementNode {
  protected $properties = array(
    'matchOn' => NULL,
    'body' => NULL,
  );

  /**
   * @return ExpressionNode
   */
  public function getMatchOn() {
    return $this->properties['matchOn'];
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
