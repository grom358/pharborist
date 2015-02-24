<?php
namespace Pharborist;

class FileUtil {

  /**
   * Recursively find files in a directory with matching extensions.
   *
   * @param string $directory
   *   Path of directory to search.
   * @param array $extensions
   *   (Optional) Array of file extensions of files to find.
   * @return array
   *   List of files found with matching extensions.
   */
  public static function findFiles($directory, $extensions = ['php']) {
    if (!is_dir($directory)) {
      return [];
    }
    $directory_iterator = new \RecursiveDirectoryIterator($directory);
    $iterator = new \RecursiveIteratorIterator($directory_iterator);
    $pattern = '/^.+\.(' . implode('|', $extensions) . ')$/i';
    $regex = new \RegexIterator($iterator, $pattern, \RecursiveRegexIterator::GET_MATCH);
    $files = [];
    foreach ($regex as $name => $object) {
      $files[] = $name;
    }
    return $files;
  }

}
