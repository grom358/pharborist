<?php
namespace Pharborist\Constants;

use Pharborist\ClassStatementNode;
use Pharborist\DocCommentTrait;
use Pharborist\InterfaceStatementNode;

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
   * @return ConstantDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->declarations->getItems();
  }
}
