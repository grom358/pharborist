<?php
namespace Pharborist;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
    'name' => NULL,
    'extends' => array(),
    'statements' => array(),
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
    return $this->properties['extends'];
  }

  /**
   * @return (InterfaceMethodNode|ConstantDeclarationNode)[]
   */
  public function getStatements() {
    return $this->properties['statements'];
  }
}
