<?php
namespace Pharborist\Index;

class ProjectIndex {

  use ClassContainerTrait;
  use ConstantContainerTrait;
  use FunctionContainerTrait;
  use InterfaceContainerTrait;
  use TraitContainerTrait;

  /**
   * @var string[]
   */
  private $directories = [];

  /**
   * @var FileIndex[]
   */
  private $files = [];

  /**
   * @var NamespaceIndex[]
   */
  private $namespaces = [];

  /**
   * @param string[] $directories
   * @param FileIndex[] $files
   * @param ClassIndex[] $classes
   */
  public function __construct($directories, $files, $classes) {
    $this->directories = $directories;
    $this->files = $files;
    $this->classes = $classes;
  }

  /**
   * Sub directories in this project containing source code.
   *
   * @return string[]
   */
  public function getDirectories() {
    return $this->directories;
  }

  /**
   * @return FileIndex[]
   */
  public function getFiles() {
    return $this->files;
  }

  /**
   * Get file index for filename.
   *
   * @param string $filename
   *   Path of file to get index for.
   * @return FileIndex
   */
  public function getFileIndex($filename) {
    return isset($this->files[$filename]) ? $this->files[$filename] : NULL;
  }

  /**
   * @return NamespaceIndex[]
   */
  public function getNamespaces() {
    return $this->namespaces;
  }

  /**
   * @return NamespaceIndex|NULL
   */
  public function getNamespace($ns) {
    return $this->namespaces[$ns];
  }

  /**
   * @return boolean
   */
  public function hasNamespace($ns) {
    return isset($this->namespaces[$ns]);
  }
}
