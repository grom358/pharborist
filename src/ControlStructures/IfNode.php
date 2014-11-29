<?php
namespace Pharborist\ControlStructures;

use Pharborist\Node;
use Pharborist\NodeCollection;
use Pharborist\ParenTrait;
use Pharborist\StatementNode;
use Pharborist\ExpressionNode;
use Pharborist\TokenNode;

/**
 * An if control structure.
 */
class IfNode extends StatementNode {
  use ParenTrait;
  use AltSyntaxTrait;

  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @var Node
   */
  protected $then;

  /**
   * @var TokenNode
   */
  protected $elseKeyword;

  /**
   * @var TokenNode
   */
  protected $elseColon;

  /**
   * @var Node
   */
  protected $else;

  /**
   * @return ExpressionNode
   */
  public function getCondition() {
    return $this->condition;
  }

  /**
   * @return Node
   */
  public function getThen() {
    return $this->then;
  }

  /**
   * @return NodeCollection|ElseIfNode[]
   */
  public function getElseIfs() {
    return new NodeCollection($this->childrenByInstance('\Pharborist\ControlStructures\ElseIfNode'), FALSE);
  }

  /**
   * The T_ELSE keyword token.
   *
   * @return TokenNode
   */
  public function getElseKeyword() {
    return $this->elseKeyword;
  }

  /**
   * The colon ':' token when using alternative syntax.
   *
   * @return TokenNode
   */
  public function getElseColon() {
    return $this->elseColon;
  }

  /**
   * @return Node
   */
  public function getElse() {
    return $this->else;
  }
}
