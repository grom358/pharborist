<?php
namespace Pharborist;

/**
 * An exit.
 */
class ExitNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'status' => NULL,
  );

  /**
   * @return Node
   */
  public function getStatus() {
    return $this->properties['status'];
  }
}
