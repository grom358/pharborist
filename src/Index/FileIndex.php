<?php
namespace Pharborist\Index;

/**
 * Index information about a file.
 */
class FileIndex {

  /**
   * @var string
   */
  private $filename;

  /**
   * @var int
   */
  private $lastIndexed;

  /**
   * @var string
   */
  private $hash;

  /**
   * @var string[]
   */
  private $classes;

  /**
   * @var string[]
   */
  private $traits;

  /**
   * @var string[]
   */
  private $interfaces;

  /**
   * @var string[]
   */
  private $constants;

  /**
   * @var string[]
   */
  private $functions;

  /**
   * @param string $filename
   * @param int $lastIndexed
   * @param string $hash
   * @param string[] $classes
   * @param string[] $traits
   * @param string[] $interfaces
   * @param string[] $constants
   * @param string[] $functions
   */
  public function __construct($filename, $lastIndexed, $hash, $classes, $traits, $interfaces, $constants, $functions) {
    $this->filename = $filename;
    $this->lastIndexed = $lastIndexed;
    $this->hash = $hash;
    $this->classes = $classes;
    $this->traits = $traits;
    $this->interfaces = $interfaces;
    $this->constants = $constants;
    $this->functions = $functions;
  }

  /**
   * Relative path to file.
   *
   * @return string
   */
  public function getFilename() {
    return $this->filename;
  }

  /**
   * Unix timestamp of when file was last indexed.
   *
   * @return int
   */
  public function getLastIndexed() {
    return $this->lastIndexed;
  }

  /**
   * Return MD5 hash of file when was last indexed.
   *
   * @return string
   */
  public function getHash() {
    return $this->hash;
  }

  /**
   * Get the fully qualified names of the classes defined by this file.
   *
   * @return string[]
   */
  public function getClasses() {
    return $this->classes;
  }

  /**
   * Get the fully qualified names of the traits defined by this file.
   *
   * @return string[]
   */
  public function getTraits() {
    return $this->traits;
  }

  /**
   * Get the fully qualified names of the interfaces defined by this file.
   *
   * @return string[]
   */
  public function getInterfaces() {
    return $this->interfaces;
  }

  /**
   * Get the fully qualified names of the constants defined by this file.
   *
   * @return string[]
   */
  public function getConstants() {
    return $this->constants;
  }

  /**
   * Get the fully qualified names of the functions defined by this file.
   *
   * @return string[]
   */
  public function getFunctions() {
    return $this->functions;
  }

}
