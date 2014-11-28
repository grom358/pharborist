<?php
namespace Pharborist\ControlStructures;

use Pharborist\Node;
use Pharborist\NodeCollection;
use Pharborist\ParenTrait;
use Pharborist\StatementNode;
use Pharborist\CommaListNode;

/**
 * A declare control structure.
 */
class DeclareNode extends StatementNode {
  use ParenTrait;

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
   * @return NodeCollection|DeclareDirectiveNode[]
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
