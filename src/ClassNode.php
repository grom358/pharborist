<?php
namespace Pharborist;

/**
 * Class declaration.
 */
class ClassNode extends StatementNode {
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
  public function getAbstract() {
    return $this->abstract;
  }

  /**
   * @return TokenNode
   */
  public function getFinal() {
    return $this->final;
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
