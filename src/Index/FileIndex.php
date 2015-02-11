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
   * @param string $filename
   * @param int $lastIndexed
   * @param string $hash
   */
  public function __construct($filename, $lastIndexed, $hash) {
    $this->filename = $filename;
    $this->lastIndexed = $lastIndexed;
    $this->hash = $hash;
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
