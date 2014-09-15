<?php
namespace Pharborist;

/**
 * Namespace declaration.
 */
class NamespaceNode extends StatementNode {
  use DocCommentTrait;

  /**
   * Create namespace declaration.
   *
   * @param NameNode|string $name
   *   Namespace path.
   *
   * @return NamespaceNode
   */
  public static function create($name) {
    $name = (string) $name;
    $name = ltrim($name, '\\');
    $namespace_node = Parser::parseSnippet("namespace $name;");
    return $namespace_node;
  }

  /**
   * @var NameNode
   */
  protected $name;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @return NameNode
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
