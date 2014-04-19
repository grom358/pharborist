<?php
namespace Pharborist;

/**
 * A case statement in switch control structure.
 */
class CaseNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  protected $matchOn;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @return ExpressionNode
   */
  public function getMatchOn() {
    return $this->matchOn;
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
