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
    return $this->closest(Filter::isInstanceOf('\Pharborist\Namespaces\NamespaceNode'));
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
      return strpos($this->getFullyQualifiedName(), $ns) === 0;
    }
    elseif ($ns instanceof NamespaceNode) {
      return $this->inNamespace($ns->getFullyQualifiedName());
    }
    else {
      throw new \InvalidArgumentException();
    }
  }

  /**
   * @see NameResolutionInterface::getFullyQualifiedName()
   */
  public function getFullyQualifiedName() {
    return '\\' . $this->getNamespace()->getFullyQualifiedName() . '\\' . $this->getQualifiedName();
  }

  /**
   * @see NameResolutionInterface::getQualifiedName()
   */
  public function getQualifiedName() {
    return $this->getUnqualifiedName();
  }

  /**
   * @see NameResolutionInterface::getUnqualifiedName()
   */
  public function getUnqualifiedName() {
    return $this->name->getText();
  }
}
