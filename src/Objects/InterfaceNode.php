<?php
namespace Pharborist\Objects;

use Pharborist\Filter;
use Pharborist\Namespaces\IdentifierNameTrait;
use Pharborist\NodeCollection;
use Pharborist\Namespaces\NameNode;
use Pharborist\StatementNode;
use Pharborist\DocCommentTrait;
use Pharborist\CommaListNode;
use Pharborist\StatementBlockNode;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  use IdentifierNameTrait;
  use DocCommentTrait;

  /**
   * @var CommaListNode
   */
  protected $extends;

  /**
   * @var StatementBlockNode
   */
  protected $statements;

  /**
   * @return CommaListNode
   */
  public function getExtendList() {
    return $this->extends;
  }

  /**
   * @return NodeCollection|NameNode[]
   */
  public function getExtends() {
    return $this->extends->getItems();
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->statements;
  }

  /**
   * @return NodeCollection|InterfaceStatementNode[]
   */
  public function getStatements() {
    return $this->statements->getStatements();
  }

  /**
   * @return NodeCollection|InterfaceMethodNode[]
   */
  public function getMethods() {
    return $this->statements->children(Filter::isInstanceOf('\Pharborist\Objects\InterfaceMethodNode'));
  }

  /**
   * Returns a method by name, if it exists.
   *
   * @param string $name
   *  The method name.
   *
   * @return InterfaceMethodNode|NULL
   */
  public function getMethod($name) {
    $methods = $this
      ->getMethods()
      ->filter(function (InterfaceMethodNode $method) use ($name) {
        return $method->getName()->getText() === $name;
      });
    return $methods->isEmpty() ? NULL : $methods[0];
  }

  /**
   * Returns if the interface has the named method.
   *
   * @param string $name
   *  The method name.
   *
   * @return boolean
   */
  public function hasMethod($name) {
    return in_array($name, $this->getMethodNames());
  }

  /**
   * Returns the names of all interface methods.
   *
   * @return string[]
   */
  public function getMethodNames() {
    return array_map(function (InterfaceMethodNode $node) {
      return $node->getName()->getText();
    }, $this->getMethods()->toArray());
  }
}
