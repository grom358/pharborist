<?php
namespace Pharborist;

/**
 * An echo statement.
 */
class EchoStatementNode extends StatementNode {
  protected $properties = array(
    'expressions' => array(),
  );

  /**
   * @return ExpressionNode[]
   */
  public function getExpressions() {
    return $this->properties['expressions'];
  }
}
