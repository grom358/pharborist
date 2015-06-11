<?php
namespace Pharborist\Index;

use Pharborist\FileUtil;

/**
 * Entry point to index for project.
 */
class ProjectIndex {

  /**
   * @var FileSet
   */
  private $fileSet;

  /**
   * @var FileIndex[]
   */
  private $files;

  /**
   * @var ClassIndex[]
   */
  private $classes;

  /**
   * @var TraitIndex[]
   */
  private $traits;

  /**
   * @var InterfaceIndex[]
   */
  private $interfaces;

  /**
   * @var ConstantIndex[]
   */
  private $constants;

  /**
   * @var FunctionIndex[]
   */
  private $functions;

  /**
   * @var string[]
   */
  private $errors;

  /**
   * Build a project index.
   *
   * @param FileSet $fileSet
   * @param FileIndex[] $files
   * @param ClassIndex[] $classes
   * @param TraitIndex[] $traits
   * @param InterfaceIndex[] $interfaces
   * @param ConstantIndex[] $constants
   * @param FunctionIndex[] $functions
   * @param string[] $errors
   */
  public function __construct(
    FileSet $fileSet,
    array $files,
    array $classes,
    array $traits,
    array $interfaces,
    array $constants,
    array $functions,
    array $errors
  ) {
    $this->fileSet = $fileSet;
    $this->files = $files;
    $this->classes = $classes;
    $this->traits = $traits;
    $this->interfaces = $interfaces;
    $this->constants = $constants;
    $this->functions = $functions;
    $this->errors = $errors;
  }

  /**
   * Get the file set for this project index.
   *
   * @return FileSet
   */
  public function getFileSet() {
    return $this->fileSet;
  }

  /**
   * Test if file exists.
   *
   * @param string $filename
   *   Filename.
   *
   * @return bool
   *   TRUE if file exists.
   */
  public function fileExists($filename) {
    return isset($this->files[$filename]);
  }

  /**
   * @return FileIndex[]
   */
  public function getFiles() {
    return $this->files;
  }

  /**
   * Test if class exists.
   *
   * @param string $class_fqn
   *   Fully qualified class name.
   *
   * @return bool
   *   TRUE if class exists.
   */
  public function classExists($class_fqn) {
    return isset($this->classes[$class_fqn]);
  }

  /**
   * @return ClassIndex[]
   */
  public function getClasses() {
    return $this->classes;
  }

  /**
   * Get class from index.
   *
   * @param string $class_fqn
   *   Fully qualified class name.
   *
   * @return ClassIndex
   */
  public function getClass($class_fqn) {
    return $this->classes[$class_fqn];
  }

  /**
   * Test if trait exists.
   *
   * @param string $trait_fqn
   *   Fully qualified trait name.
   *
   * @return bool
   *   TRUE if trait exists.
   */
  public function traitExists($trait_fqn) {
    return isset($this->traits[$trait_fqn]);
  }

  /**
   * @return TraitIndex[]
   */
  public function getTraits() {
    return $this->traits;
  }

  /**
   * Get trait from index.
   *
   * @param string $trait_fqn
   *   Fully qualified trait name.
   *
   * @return TraitIndex
   */
  public function getTrait($trait_fqn) {
    return $this->traits[$trait_fqn];
  }

  /**
   * Test if interface exists.
   *
   * @param string $interface_fqn
   *   Fully qualified interface name.
   *
   * @return bool
   *   TRUE if interface exists.
   */
  public function interfaceExists($interface_fqn) {
    return isset($this->interfaces[$interface_fqn]);
  }

  /**
   * @return InterfaceIndex[]
   */
  public function getInterfaces() {
    return $this->interfaces;
  }

  /**
   * Get interface from index.
   *
   * @param string $interface_fqn
   *   Fully qualified interface name.
   *
   * @return InterfaceIndex
   */
  public function getInterface($interface_fqn) {
    return $this->interfaces[$interface_fqn];
  }

  /**
   * Test if constant exists.
   *
   * @param string $constant_fqn
   *   Fully qualified constant name.
   *
   * @return bool
   *   TRUE if interface exists.
   */
  public function constantExists($constant_fqn) {
    return isset($this->constants[$constant_fqn]);
  }

  /**
   * @return ConstantIndex[]
   */
  public function getConstants() {
    return $this->constants;
  }

  /**
   * Get constant from index.
   *
   * @param string $constant_fqn
   *   Fully qualified constant name.
   *
   * @return ConstantIndex
   */
  public function getConstant($constant_fqn) {
    return $this->constants[$constant_fqn];
  }

  /**
   * Test if function exists.
   *
   * @param string $function_fqn
   *   Fully qualified constant name.
   *
   * @return bool
   *   TRUE if function exists.
   */
  public function functionExists($function_fqn) {
    return isset($this->functions[$function_fqn]);
  }

  /**
   * @return FunctionIndex[]
   */
  public function getFunctions() {
    return $this->functions;
  }

  /**
   * Get function from index.
   *
   * @param string $function_fqn
   *   Fully qualified function name.
   *
   * @return FunctionIndex
   */
  public function getFunction($function_fqn) {
    return $this->functions[$function_fqn];
  }

  /**
   * Get index errors.
   *
   * @return string[]
   */
  public function getErrors() {
    return $this->errors;
  }

  /**
   * Load index from filesystem.
   *
   * @param string $dir
   *   Directory to load index from.
   *
   * @return ProjectIndex
   */
  public static function load($dir) {
    return unserialize(file_get_contents($dir . '/.pharborist'));
  }

  /**
   * Delete index from filesystem.
   *
   * @param string $dir
   *   Directory to load index from.
   *
   * @return bool
   *   TRUE if index was deleted.
   */
  public static function delete($dir) {
    return @unlink($dir . '/.pharborist');
  }

  /**
   * Save index to filesystem.
   *
   * @param string $dir
   *   Directory to save index to.
   *
   * @return $this
   */
  public function save($dir) {
    file_put_contents($dir . '/.pharborist', serialize($this));
    return $this;
  }
}
