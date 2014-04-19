<?php
namespace Pharborist;

/**
 * Namespace declaration.
 */
class NamespaceNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var NamespacePathNode
   */
  protected $name;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return NamespacePathNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
