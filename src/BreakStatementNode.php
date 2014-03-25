<?php
namespace Pharborist;

/**
 * A break statement.
 */
class BreakStatementNode extends StatementNode {
  /**
   * An optional numeric argument which tells break how many nested enclosing
   * structures are to be broken out of.
   * @var IntegerNode
   */
  public $level = 1;
}
