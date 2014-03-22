<?php
namespace Pharborist;

/**
 * A break statement
 * @package Pharborist
 */
class BreakStatementNode extends StatementNode {
  /**
   * An optional numeric argument which tells break how many nested enclosing
   * structures are to be broken out of.
   * @var IntegerNode
   */
  public $level = 1;
}
