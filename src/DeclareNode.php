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
   * @var CommaListNode
   */
  protected $directives;

  /**
   * @return CommaListNode
   */
  public function getDirectiveList() {
    return $this->directives;
  }

  /**
   * @return DeclareDirectiveNode[]
   */
  public function getDirectives() {
    return $this->directives->getItems();
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->body;
  }
}
