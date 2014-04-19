<?php
namespace Pharborist;

/**
 * A unset statement.
 */
class UnsetStatementNode extends StatementNode {
  /**
   * @var UnsetNode
   */
  protected $functionCall;

  /**
   * @return UnsetNode
   */
  public function getFunctionCall() {
    return $this->functionCall;
  }
}
