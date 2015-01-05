<?php
namespace Pharborist\Namespaces;

use Pharborist\Constants\ConstantNode;
use Pharborist\Filter;
use Pharborist\Functions\FunctionCallNode;
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
class NameNode extends ParentNode {
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
  public function getNamespace() {
    return $this->closest(Filter::isInstanceOf('\Pharborist\Namespaces\NamespaceNode'));
  }

  /**
   * @return string
   */
  public function getParentPath() {
    if ($this->parent instanceof NamespaceNode) {
      return '\\';
    }
    if ($this->isAbsolute()) {
      return '\\';
    }
    /** @var NamespaceNode $namespace */
    $namespace = $this->getNamespace();
    if (!$namespace) {
      return '\\';
    }
    else {
      $name = $namespace->getName();
      if (!$name) {
        return '\\';
      }
      return '\\' . $name->getPath() . '\\';
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
   * Resolve an unqualified name to fully qualified name.
   *
   * @param string $name
   *   The unqualified name to resolve.
   *
   * @return string
   *   Fully qualified name.
   */
  protected function resolveUnqualified($name) {
    if ($this->parent instanceof NamespaceNode) {
      return '\\' . $name;
    }
    if ($this->parent instanceof UseDeclarationNode) {
      return '\\' . $name;
    }
    $namespace = $this->getNamespace();
    $use_declarations = array();
    if ($namespace) {
      $use_declarations = $namespace->getBody()->getUseDeclarations();
    }
    else {
      /** @var \Pharborist\RootNode $root_node */
      $root_node = $this->closest(Filter::isInstanceOf('\Pharborist\RootNode'));
      if ($root_node) {
        $use_declarations = $root_node->getUseDeclarations();
      }
    }
    if ($this->parent instanceof FunctionCallNode) {
      /** @var UseDeclarationNode $use_declaration */
      foreach ($use_declarations as $use_declaration) {
        if ($use_declaration->isFunction() && $use_declaration->getBoundedName() === $name) {
          return '\\' . $use_declaration->getName()->getPath();
        }
      }
      return $this->getParentPath() . $name;
    }
    elseif ($this->parent instanceof ConstantNode) {
      /** @var UseDeclarationNode $use_declaration */
      foreach ($use_declarations as $use_declaration) {
        if ($use_declaration->isConst() && $use_declaration->getBoundedName() === $name) {
          return '\\' . $use_declaration->getName()->getPath();
        }
      }
      return $this->getParentPath() . $name;
    }
    else {
      // Name is a class reference.
      /** @var UseDeclarationNode $use_declaration */
      foreach ($use_declarations as $use_declaration) {
        if ($use_declaration->isClass() && $use_declaration->getBoundedName() === $name) {
          return '\\' . $use_declaration->getName()->getPath();
        }
      }
      // No use declaration so class name refers to class in current namespace.
      return $this->getParentPath() . $name;
    }
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
      $path = $absolute ? '\\' : $this->getParentPath();
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
    return $this->getParentPath() === '\\';
  }


  /**
   * Returns trailing name component of path.
   *
   * @return string
   *   Last component of path.
   */
  public function getBaseName() {
    $path = $this->getAbsolutePath();
    $parts = explode('\\', $path);
    return end($parts);
  }
}
