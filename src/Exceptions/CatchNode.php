<?php
namespace Pharborist\Exceptions;

use Pharborist\ParentNode;
use Pharborist\Namespaces\NameNode;
use Pharborist\ParenTrait;
use Pharborist\StatementBlockNode;
use Pharborist\Variables\VariableNode;

/**
 * A catch in a try control structure.
 */
class CatchNode extends ParentNode {
  use ParenTrait;

  /**
   * @var NameNode
   */
  protected $exceptionType;

  /**
   * @var VariableNode
   */
  protected $variable;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @return NameNode
   */
  public function getExceptionType() {
    return $this->exceptionType;
  }

  /**
   * Returns the variable for the caught exception.
   *
   * @return VariableNode
   */
  public function getVariable() {
    return $this->variable;
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
