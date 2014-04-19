<?php
namespace Pharborist;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

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
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return NamespacePathNode[]
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
