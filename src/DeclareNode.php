<?php
namespace Pharborist;

/**
 * A declare control structure.
 */
class DeclareNode extends StatementNode {
  protected $properties = array(
    'directives' => array(),
    'body' => NULL,
  );

  /**
   * @return DeclareDirectiveNode[]
   */
  public function getDirectives() {
    return $this->childrenByInstance('\Pharborist\DeclareDirectiveNode');
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
