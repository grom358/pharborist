<?php
namespace Pharborist;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  protected $properties = array(
    'name' => NULL,
    'extends' => array(),
    'statements' => array(),
  );

  /**
   * @var DocCommentNode
   */
  public $docComment;

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
