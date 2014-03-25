<?php
namespace Pharborist;

/**
 * A block of statements.
 */
class StatementBlockNode extends ParentNode {
  /**
   * @var StatementNode[]
   */
  public $statements = array();
}
