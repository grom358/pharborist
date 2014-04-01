<?php
namespace Pharborist;

/**
 * An echo statement.
 */
class EchoStatementNode extends StatementNode {
  /**
   * @var ExpressionNode[]
   */
  public $expressions = array();
}
