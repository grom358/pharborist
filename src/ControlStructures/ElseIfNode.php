<?php
namespace Pharborist\ControlStructures;

use Pharborist\ParentNode;
use Pharborist\Node;
use Pharborist\ExpressionNode;
use Pharborist\ParenTrait;
use Pharborist\TokenNode;

/**
 * elseif control structure.
 */
class ElseIfNode extends ParentNode {
  use ParenTrait;

  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @var TokenNode
   */
  protected $openColon;

  /**
   * @var Node
   */
  protected $then;

  /**
   * @return ExpressionNode
   */
  public function getCondition() {
    return $this->condition;
  }

  /**
   * The colon (':') delimiter for body of statements.
   *
   * @return TokenNode
   */
  public function getOpenColon() {
    return $this->openColon;
  }

  /**
   * @return Node
   */
  public function getThen() {
    return $this->then;
  }
}
