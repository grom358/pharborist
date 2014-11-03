<?php
namespace Pharborist\Objects;

use Pharborist\Namespaces\IdentifierNameTrait;
use Pharborist\NodeCollection;
use Pharborist\Namespaces\NameNode;
use Pharborist\StatementNode;
use Pharborist\DocCommentTrait;
use Pharborist\CommaListNode;
use Pharborist\StatementBlockNode;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  use IdentifierNameTrait;
  use DocCommentTrait;

  /**
   * @var CommaListNode
   */
  protected $extends;

  /**
   * @var StatementBlockNode
   */
  protected $statements;

  /**
   * @return NodeCollection|NameNode[]
   */
  public function getExtends() {
    return $this->extends->getItems();
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->statements;
  }

  /**
   * @return NodeCollection|InterfaceStatementNode[]
   */
  public function getStatements() {
    return $this->statements->getStatements();
  }
}
