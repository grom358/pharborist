<?php
namespace Pharborist\Namespaces;

use Pharborist\Filter;
use Pharborist\TokenNode;

/**
 * Trait that gives a node an identifier in a namespace.
 *
 * Functions, constants, classes, interfaces, and traits all have an identifier.
 */
trait IdentifierNameTrait {
  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @return NamespaceNode
   */
  public function getNamespace() {
    /** @var \Pharborist\Node $this */
    return $this->closest(Filter::isInstanceOf('\Pharborist\Namespaces\NamespaceNode'));
  }

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the identifier name of this node.
   *
   * @param string $name
   *   New name.
   * @return $this
   */
  public function setName($name) {
    $this->name->setText($name);
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
      $namespace_node = $this->getNamespace();
      $namespace = $namespace_node === NULL ? '' : $namespace_node->getName()->getAbsolutePath();
      return $ns === $namespace;
    }
    elseif ($ns instanceof NamespaceNode) {
      return $this->getNamespace() === $ns;
    }
    else {
      throw new \InvalidArgumentException();
    }
  }

  /**
   * @return string
   */
  public function getFullyQualifiedName() {
    $ns = $this->getNamespace();
    if ($ns) {
      return '\\' . $ns->getFullyQualifiedName() . '\\' . $this->getQualifiedName();
    }
    else {
      return '\\' . $this->getQualifiedName();
    }
  }

  /**
   * @return string
   */
  public function getQualifiedName() {
    return $this->getUnqualifiedName();
  }

  /**
   * @return string
   */
  public function getUnqualifiedName() {
    return $this->name->getText();
  }

  /**
   * @return string
   */
  public function getQualifiedRelativeName() {
    $full_name = $this->getFullyQualifiedName();
    $ns_name = $this->getNamespace()->getFullyQualifiedName();
    return substr($full_name, strlen($ns_name));
  }
}
