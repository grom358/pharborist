<?php
namespace Pharborist;

/**
 * A declare control structure.
 */
class DeclareNode extends StatementNode {
  /**
   * @var Node
   */
  protected $body;

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
    return $this->body;
  }
}
