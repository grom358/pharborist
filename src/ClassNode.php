<?php
namespace Pharborist;

/**
 * Class declaration.
 */
class ClassNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
    'abstract' => NULL,
    'final' => NULL,
    'name' => NULL,
    'extends' => NULL,
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }

  /**
   * @return TokenNode
   */
  public function getAbstract() {
    return $this->properties['abstract'];
  }

  /**
   * @return TokenNode
   */
  public function getFinal() {
    return $this->properties['final'];
  }

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->properties['name'];
  }

  /**
   * @return NamespacePathNode
   */
  public function getExtends() {
    return $this->properties['extends'];
  }

  /**
   * @return NamespacePathNode[]
   */
  public function getImplements() {
    /** @var CommaListNode $implements */
    $implements = $this->properties['implements'];
    return $implements->getItems();
  }

  /**
   * @return (ClassMemberListNode|ClassMethodNode|ConstantDeclarationNode|TraitUseNode)[]
   */
  public function getStatements() {
    /** @var StatementBlockNode $statement_block */
    $statement_block = $this->properties['statements'];
    return $statement_block->getStatements();
  }
}
