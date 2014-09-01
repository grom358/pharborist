<?php
namespace Pharborist;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  use DocCommentTrait;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var CommaListNode
   */
  protected $extends;

  /**
   * @var StatementBlockNode
   */
  protected $statements;

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return NameNode[]
   */
  public function getExtends() {
    return $this->extends->getItems();
  }

  /**
   * @return InterfaceStatementNode[]
   */
  public function getStatements() {
    return $this->statements->getStatements();
  }
}
