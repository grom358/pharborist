<?php
namespace Pharborist\Index;

/**
 * Index information about a file.
 */
class FileIndex extends NamespaceIndex {

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
   * @param string $filename
   * @param int $lastIndexed
   * @param string $hash
   * @param string[] $classes
   */
  public function __construct($filename, $lastIndexed, $hash, $classes) {
    $this->filename = $filename;
    $this->lastIndexed = $lastIndexed;
    $this->hash = $hash;
    $this->classes = $classes;
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

}
