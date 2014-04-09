<?php
namespace Pharborist;

class ExpressionListNode extends ParentNode {
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
