<?php
namespace Pharborist\Index;

class ProjectIndex {

  /**
   * @var string[]
   */
  private $directories = [];

  /**
   * @var FileIndex[]
   */
  private $files = [];

  /**
   * @var ClassIndex[]
   */
  private $classes = [];

  /**
   * Sub directories in this project containing source code.
   *
   * @return string[]
   */
  public function getDirectories() {
    return $this->directories;
  }

  /**
   * @param string $directory
   */
  public function addDirectory($directory) {
    $this->directories[] = $directory;
  }

  /**
   * @return ClassIndex[]
   */
  public function getClasses() {
    return $this->classes;
  }

  public function addClass(ClassIndex $classIndex) {
    $this->classes[$classIndex->getName()] = $classIndex;
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
   * Add (or update) file index.
   *
   * @param FileIndex $file_index
   *   File index to add to project index.
   *
   * @return $this
   */
  public function addFile(FileIndex $file_index) {
    $this->files[$file_index->getFilename()] = $file_index;
    return $this;
  }
}
