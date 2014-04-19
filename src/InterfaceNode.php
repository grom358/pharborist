<?php
namespace Pharborist;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
    'name' => NULL,
    'extends' => NULL,
    'statements' => NULL,
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }

  /**
   * @return Node
   */
  public function getName() {
    return $this->properties['name'];
  }

  /**
   * @return NamespacePathNode[]
   */
  public function getExtends() {
    /** @var CommaListNode $extends */
    $extends = $this->properties['extends'];
    return $extends->getItems();
  }

  /**
   * @return (InterfaceMethodNode|ConstantDeclarationNode)[]
   */
  public function getStatements() {
    /** @var StatementBlockNode $statement_block */
    $statement_block = $this->properties['statements'];
    return $statement_block->getStatements();
  }
}
