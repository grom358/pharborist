<?php
namespace Pharborist\Index;

use Pharborist\FileUtil;

/**
 * Set of files.
 */
class FileSet {

  /**
   * @var string[]
   */
  private $directories;

  /**
   * @var string[]
   */
  private $extensions;

  /**
   * Default file set is to include .php files.
   *
   * @param string[] $directories
   * @param string[] $extensions
   */
  public function __construct($directories = [], $extensions = ['php']) {
    $this->directories = $directories;
    $this->extensions = $extensions;
  }

  /**
   * @return string[]
   */
  public function getDirectories() {
    return $this->directories;
  }

  /**
   * Add directory to file set.
   *
   * @param string $directory
   *   Path of directory to add.
   *
   * @return $this
   */
  public function addDirectory($directory) {
    $this->directories[] = $directory;
    return $this;
  }

  /**
   * Scan for files in the file set.
   *
   * @return string[]
   *   Files currently in this set.
   */
  public function scan() {
    $files = [];
    foreach ($this->directories as $directory) {
      $files = array_merge($files, FileUtil::findFiles($directory, $this->extensions));
    }
    return $files;
  }
}
