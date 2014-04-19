<?php
namespace Pharborist;

/**
 * A goto statement.
 */
class GotoStatementNode extends StatementNode {
  /**
   * @var TokenNode
   */
  protected $label;

  /**
   * @return TokenNode
   */
  public function getLabel() {
    return $this->label;
  }
}
