<?php
namespace Pharborist;

/**
 * A trait declaration.
 */
class TraitNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
    'name' => NULL,
    'extends' => NULL,
    'implements' => NULL,
    'statements' => NULL,
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
