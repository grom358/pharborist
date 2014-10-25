<?php
namespace Pharborist\ControlStructures;

use Pharborist\StatementNode;
use Pharborist\ExpressionNode;
use Pharborist\StatementBlockNode;

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
