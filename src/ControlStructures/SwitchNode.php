<?php
namespace Pharborist\ControlStructures;

use Pharborist\NodeCollection;
use Pharborist\ParenTrait;
use Pharborist\StatementNode;
use Pharborist\ExpressionNode;

/**
 * A switch control structure.
 */
class SwitchNode extends StatementNode {
  use ParenTrait;
  use AltSyntaxTrait;

  /**
   * @var ExpressionNode
   */
  protected $switchOn;

  /**
   * @return ExpressionNode
   */
  public function getSwitchOn() {
    return $this->switchOn;
  }

  /**
   * @return NodeCollection|CaseNode[]
   */
  public function getCases() {
    return new NodeCollection($this->childrenByInstance('\Pharborist\StatementNode'), FALSE);
  }
}
