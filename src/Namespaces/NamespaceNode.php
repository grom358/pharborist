<?php
namespace Pharborist\Namespaces;

use Pharborist\Constants\ConstantDeclarationNode;
use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Objects\ClassNode;
use Pharborist\Objects\InterfaceNode;
use Pharborist\Objects\TraitNode;
use Pharborist\Parser;
use Pharborist\StatementNode;
use Pharborist\DocCommentTrait;
use Pharborist\StatementBlockNode;

/**
 * A namespace declaration.
 *
 * This node is used for both forms of namespace declaration:
 *
 * ```
 * namespace \Foo\Bar;
 * ```
 * and
 * ```
 * namespace \Foo\Baz {
 *  // Do amazing things here
 * }
 * ```
 *
 * Everything in the namespace is part of the namespace's body. This is
 * the case even with the first form of the namespace. A parsed file with
 * a namespace declaration at the top will have the declaration as one of
 * its children (alongside any preceding comments and whitespace), and
 * everything else in the file after the namespace declaration will be
 * a descendant of that NamespaceNode.
 *
 * @see \Pharborist\NameNode
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

  /**
   * Determine if the node belongs to this namespace.
   *
   * @param ClassNode|InterfaceNode|TraitNode|FunctionDeclarationNode|ConstantDeclarationNode $node
   *   Node to test if owned by namespace.
   *
   * @return bool
   */
  public function owns($node) {
    return $this === $node->getName()->getNamespace();
  }

  /**
   * Get use declarations.
   *
   * @return UseDeclarationNode[]|\Pharborist\NodeCollection
   *   Use declarations.
   */
  public function getUseDeclarations() {
    return $this->body->getUseDeclarations();
  }

  /**
   * Return mapping of class names to fully qualified names.
   *
   * @return array
   *   Associative array of namespace alias to fully qualified names.
   */
  public function getClassAliases() {
    return $this->body->getClassAliases();
  }
}
