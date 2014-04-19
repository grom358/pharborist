<?php
namespace Pharborist;

/**
 * A trait declaration.
 */
class TraitNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var TokenNode
   */
  protected $abstract;

  /**
   * @var TokenNode
   */
  protected $final;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var NamespacePathNode
   */
  protected $extends;

  /**
   * @var CommaListNode
   */
  protected $implements;

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
   * @return NamespacePathNode
   */
  public function getExtends() {
    return $this->extends;
  }

  /**
   * @return NamespacePathNode[]
   */
  public function getImplements() {
    return $this->implements->getItems();
  }

  /**
   * @return (ClassMemberListNode|ClassMethodNode|ConstantDeclarationNode|TraitUseNode)[]
   */
  public function getStatements() {
    return $this->statements->getStatements();
  }
}
