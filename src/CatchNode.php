<?php
namespace Pharborist;

/**
 * A catch in a try control structure.
 */
class CatchNode extends ParentNode {
  protected $properties = array(
    'exceptionType' => NULL,
    'variable' => NULL,
    'body' => NULL,
  );

  /**
   * @return NamespacePathNode
   */
  public function getExceptionType() {
    return $this->properties['exceptionType'];
  }

  /**
   * @return VariableNode
   */
  public function getVariable() {
    return $this->properties['variable'];
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
