<?php
namespace Pharborist\Namespaces;

use Pharborist\TokenNode;

/**
 * Trait that gives a node an identifier in a namespace.
 *
 * Functions, constants, classes, interfaces, and traits all have an identifier.
 */
trait IdentifierNameTrait {
  /**
   * @var NameNode
   */
  protected $name;

  /**
   * @return NameNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return NamespaceNode
   */
  public function getNamespace() {
    return $this->name->getNamespace();
  }

  /**
   * Set the identifier name of this node.
   *
   * @param string $name
   *   New name.
   * @return $this
   */
  public function setName($name) {
    /** @var TokenNode $identifier */
    $identifier = $this->name->firstChild();
    $identifier->setText($name);
    return $this;
  }

  /**
   * Determine if this node belongs to namespace.
   *
   * @param string|NamespaceNode $ns
   *   Either the absolute namespace path or a NamespaceNode.
   *
   * @return boolean
   *   TRUE if the node belongs to the given namespace.
   */
  public function inNamespace($ns) {
    if (is_string($ns)) {
      $namespace_node = $this->name->getNamespace();
      $namespace = $namespace_node === NULL ? '' : $namespace_node->getName()->getAbsolutePath();
      return $ns === $namespace;
    }
    elseif ($ns instanceof NamespaceNode) {
      return $this->name->getNamespace() === $ns;
    }
    else {
      throw new \InvalidArgumentException();
    }
  }
}
