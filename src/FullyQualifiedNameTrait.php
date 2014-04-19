<?php
namespace Pharborist;

trait FullyQualifiedNameTrait {
  /**
   * @var string
   */
  protected $fullyQualifiedName;

  /**
   * @return string
   */
  public function getFullyQualifiedName() {
    return $this->fullyQualifiedName;
  }

  /**
   * @param string $namespace
   * @param string $name
   * @return $this
   */
  public function setFullyQualifiedName($namespace, $name) {
    if ($namespace) {
      $this->fullyQualifiedName = '\\' . $namespace . '\\' . $name;
    }
    else {
      $this->fullyQualifiedName = '\\' . $name;
    }
    return $this;
  }
}
