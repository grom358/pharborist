<?php
namespace Pharborist;

/**
 * Class declaration.
 */
class ClassNode extends StatementNode {
  protected $properties = array(
    'abstract' => NULL,
    'final' => NULL,
    'name' => NULL,
    'extends' => NULL,
    'implements' => array(),
    'statements' => array(),
  );

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
    return $this->properties['implements'];
  }

  /**
   * @return (ClassMemberListNode|ClassMethodNode|ConstantDeclarationNode|TraitUseNode)[]
   */
  public function getStatements() {
    return $this->properties['statements'];
  }
}
