<?php
namespace Pharborist;

/**
 * A namespace path to function, constant, class, trait or interface.
 *
 * For example, MyNamespace\MyClass
 */
class NameNode extends ParentNode {
  /**
   * @var string
   */
  protected $basePath;

  /**
   * @var string
   */
  protected $alias;

  /**
   * @param string $base
   */
  public function setBase($base) {
    $this->basePath = $base;
  }

  /**
   * @param string $alias
   */
  public function setAlias($alias) {
    $this->alias = $alias;
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

  public function getAbsolutePath() {
    /** @var TokenNode[] $parts */
    $info = $this->getPathInfo();
    $absolute = $info['absolute'];
    $parts = $info['parts'];
    if ($this->alias) {
      $path = $this->alias;
      unset($parts[0]);
      if (!empty($parts)) {
        $path .= '\\';
      }
    }
    else {
      $path = '\\' . (!$absolute && $this->basePath ? $this->basePath . '\\' : '');
      if ($parts[0]->getType() === T_NAMESPACE) {
        unset($parts[0]);
      }
    }
    $path .= implode('\\', $parts);
    return $path;
  }
}
