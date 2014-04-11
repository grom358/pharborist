<?php
namespace Pharborist;

/**
 * Namespace declaration.
 */
class NamespaceNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
    'name' => NULL,
    'body' => NULL,
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
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
