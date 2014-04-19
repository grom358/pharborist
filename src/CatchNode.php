<?php
namespace Pharborist;

/**
 * A catch in a try control structure.
 */
class CatchNode extends ParentNode {
  /**
   * @var NamespacePathNode
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
   * @return NamespacePathNode
   */
  public function getExceptionType() {
    return $this->exceptionType;
  }

  /**
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
