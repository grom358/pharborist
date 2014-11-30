<?php
namespace Pharborist\ControlStructures;

use Pharborist\Node;
use Pharborist\ParenTrait;
use Pharborist\StatementNode;
use Pharborist\ExpressionNode;
use Pharborist\TokenNode;

/**
 * do-while control structure.
 */
class DoWhileNode extends StatementNode {
  use ParenTrait;

  /**
   * @var Node
   */
  protected $body;

  /**
   * @var TokenNode
   */
  protected $whileKeyword;

  /**
   * @var ExpressionNode
   */
  protected $condition;

  /**
   * @return Node
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * @return ExpressionNode
   */
  public function getCondition() {
    return $this->condition;
  }

  /**
   * The T_WHILE keyword token.
   *
   * @return TokenNode
   */
  public function getWhileKeyword() {
    return $this->whileKeyword;
  }
}
