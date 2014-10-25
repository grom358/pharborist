<?php
namespace Pharborist\ControlStructures;

use Pharborist\StatementNode;
use Pharborist\ExpressionNode;
use Pharborist\StatementBlockNode;

/**
 * A switch control structure.
 */
class SwitchNode extends StatementNode {
  /**
   * @var ExpressionNode
   */
  protected $switchOn;

  /**
   * @var StatementBlockNode
   */
  protected $cases;

  /**
   * @return ExpressionNode
   */
  public function getSwitchOn() {
    return $this->switchOn;
  }

  /**
   * @return CaseNode[]
   */
  public function getCases() {
    return $this->cases->getStatements();
  }
}
