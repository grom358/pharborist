<?php
namespace Pharborist\Constants;

use Pharborist\NodeCollection;
use Pharborist\Objects\ClassStatementNode;
use Pharborist\DocCommentTrait;
use Pharborist\Objects\InterfaceStatementNode;

/**
 * Constant declaration statement.
 */
class ConstantDeclarationStatementNode extends ClassStatementNode implements InterfaceStatementNode {
  use DocCommentTrait;

  /**
   * @var \Pharborist\CommaListNode
   */
  protected $declarations;

  /**
   * @return \Pharborist\CommaListNode
   */
  public function getDeclarationList() {
    return $this->declarations;
  }

  /**
   * @return NodeCollection|ConstantDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->declarations->getItems();
  }
}
