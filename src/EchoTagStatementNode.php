<?php
namespace Pharborist;

/**
 * A template echo tag statement.
 *
 * For example, <?=$a?>
 */
class EchoTagStatementNode extends StatementNode {
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
