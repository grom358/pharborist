<?php
namespace Pharborist\Namespaces;

use Pharborist\Filter;
use Pharborist\Functions\FunctionCallNode;
use Pharborist\NameResolutionInterface;
use Pharborist\ParentNode;
use Pharborist\Token;
use Pharborist\TokenNode;

/**
 * The name of namespace, function, constant, class, trait or interface.
 *
 * This node is used for things whose name is namespace-aware. Variables, for
 * example, cannot be namespaced, but classes can. That's why class names
 * are wrapped by NameNode: because NameNodes are aware of the namespace they
 * live in, even when moved from one namespace to another.
 */
class NameNode extends ParentNode implements NameResolutionInterface {
  /**
   * Create namespace path.
   *
   * @param string $name
   * @return NameNode
   */
  public static function create($name) {
    $parts = explode('\\', $name);
    $name_node = new NameNode();
    foreach ($parts as $i => $part) {
      $part = trim($part);
      if ($i > 0) {
        $name_node->append(Token::namespaceSeparator());
      }
      if ($part !== '') {
        $name_node->append(Token::identifier($part));
      }
    }
    return $name_node;
  }

  /**
   * Get the namespace that owns this name.
   *
   * @return NamespaceNode
   *   The namespace that owns this node.
   */
  protected function getNamespace() {
    return $this->closest(Filter::isInstanceOf('\Pharborist\Namespaces\NamespaceNode'));
  }

  /**
   * @return string
   */
  public function getBasePath() {
    if ($this->parent() instanceof NamespaceNode) {
      return '\\';
    }
    /** @var NamespaceNode $namespace */
    $namespace = $this->getNamespace();
    if (!$namespace) {
      return '\\';
    }
    else {
      return '\\' . $namespace->getName()->getText() . '\\';
    }
  }

  /**
   * Return information about the name.
   * @return array
   */
  public function getPathInfo() {
    /** @var TokenNode $first */
    $first = $this->firstChild();
    $absolute = $first->getType() === T_NS_SEPARATOR;
    $relative = $first->getType() === T_NAMESPACE;
    $parts = $this->getParts();
    return [
      'absolute' => $absolute,
      'relative' => $relative,
      'qualified' => !$absolute && count($parts) > 1,
      'unqualified' => !$absolute && count($parts) === 1,
      'parts' => $parts,
    ];
  }

  /**
   * Return TRUE if the name is an absolute name.
   * Eg. \TopNamespace\SubNamespace\MyClass
   * @return bool
   */
  public function isAbsolute() {
    /** @var TokenNode $first */
    $first = $this->firstChild();
    return $first->getType() === T_NS_SEPARATOR;
  }

  /**
   * Return TRUE if the name is a relative name.
   * Eg. namespace\MyClass
   * @return bool
   */
  public function isRelative() {
    /** @var TokenNode $first */
    $first = $this->firstChild();
    return $first->getType() === T_NAMESPACE;
  }

  /**
   * Return TRUE if the name is a qualified name.
   * Eg. MyNamespace\MyClass
   * @return bool
   */
  public function isUnqualified() {
    $absolute = $this->isAbsolute();
    $parts = $this->getParts();
    return !$absolute && count($parts) === 1;
  }

  /**
   * Return TRUE if the name is a qualified name.
   * Eg. MyNamespace\MyClass
   * @return bool
   */
  public function isQualified() {
    $absolute = $this->isAbsolute();
    $parts = $this->getParts();
    return !$absolute && count($parts) > 1;
  }

  /**
   * @return TokenNode[]
   */
  public function getParts() {
    $parts = [];
    /** @var TokenNode $child */
    $child = $this->head;
    while ($child) {
      $type = $child->getType();
      if ($type === T_NAMESPACE || $type === T_STRING) {
        $parts[] = $child;
      }
      $child = $child->next;
    }
    return $parts;
  }

  /**
   * Get the namespace path.
   * @return string
   */
  public function getPath() {
    $path = '';
    /** @var TokenNode $child */
    $child = $this->head;
    while ($child) {
      $type = $child->getType();
      if ($type === T_NAMESPACE || $type === T_NS_SEPARATOR || $type === T_STRING) {
        $path .= $child->getText();
      }
      $child = $child->next;
    }
    return $path;
  }

  /**
   * @param string $name
   *   The unqualified name to resolve.
   *
   * @return string
   */
  protected function resolveUnqualified($name) {
    if ($this->parent() instanceof NamespaceNode) {
      return '\\' . $name;
    }
    $namespace = $this->getNamespace();
    if (!$namespace) {
      return '\\' . $name;
    }
    if ($this->parent() instanceof FunctionCallNode) {
      return $this->getBasePath() . $name;
    }
    if ($this->parent() instanceof UseDeclarationNode) {
      return '\\' . $this->getPath();
    }
    /** @var UseDeclarationNode $use_declaration */
    foreach ($namespace->find(Filter::isInstanceOf('\Pharborist\Namespaces\UseDeclarationNode')) as $use_declaration) {
      $bounded_name = $use_declaration->getBoundedName();
      if ($bounded_name === $name) {
        return '\\' . $use_declaration->getName()->getPath();
      }
    }
    return $this->getBasePath() . $name;
  }

  /**
   * @return string
   *   The absolute namespace path.
   */
  public function getAbsolutePath() {
    /** @var TokenNode[] $parts */
    $info = $this->getPathInfo();
    $absolute = $info['absolute'];
    $relative = $info['relative'];
    $parts = $info['parts'];

    if (!$absolute && !$relative) {
      $path = $this->resolveUnqualified($parts[0]->getText());
      unset($parts[0]);
      if (!empty($parts)) {
        $path .= '\\';
      }
    }
    else {
      $path = $absolute ? '\\' : $this->getBasePath();
      if ($parts[0]->getType() === T_NAMESPACE) {
        unset($parts[0]);
      }
    }
    $path .= implode('\\', $parts);
    return $path;
  }

  /**
   * Returns if this name is in the global namespace, which is functionally
   * the same as having no namespace.
   *
   * @return boolean
   */
  public function isGlobal() {
    return $this->getBasePath() === '\\';
  }

  public function getFullyQualifiedName() {
    return '\\' . $this->getQualifiedName();
  }

  public function getQualifiedName() {
    return ltrim($this->getText(), '\\');
  }

  public function getUnqualifiedName() {
    $parts = $this->childrenByInstance('\Pharborist\TokenNode');
    return end($parts)->getText();
  }

  public function getQualifiedRelativeName() {
    $full_name = $this->getFullyQualifiedName();
    $ns_name = $this->getNamespace()->getFullyQualifiedName();
    return substr($full_name, strlen($ns_name));
  }
}
