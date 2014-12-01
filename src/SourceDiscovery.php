<?php
namespace Pharborist;

class SourceDiscovery {
  /**
   * @var string[]
   */
  protected $extensions;

  /**
   * @var callback
   */
  protected $callback;

  public function __construct($callback, $extensions = ['php']) {
    $this->callback = $callback;
    $this->extensions = $extensions;
  }

  public function scanDirectory($directory) {
    $directory_iterator = new \RecursiveDirectoryIterator($directory);
    $iterator = new \RecursiveIteratorIterator($directory_iterator);
    $pattern = '/^.+\.(' . implode('|', $this->extensions) . ')$/i';
    $regex = new \RegexIterator($iterator, $pattern, \RecursiveRegexIterator::GET_MATCH);
    $callback = $this->callback;
    foreach ($regex as $name => $object) {
      $callback($name);
    }
  }
}
