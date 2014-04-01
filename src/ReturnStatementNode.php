<?php
namespace Pharborist;

/**
 * A return statement.
 */
class ReturnStatementNode extends StatementNode {
  /**
   * An optional value to return.
   * @var ExpressionNode
   */
  public $value;
}
