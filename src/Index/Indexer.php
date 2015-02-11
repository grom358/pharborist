<?php
namespace Pharborist\Index;

use Pharborist\FileUtil;
use Pharborist\Objects\ClassMethodNode;
use Pharborist\Parser;
use Pharborist\RootNode;
use Pharborist\VisitorBase;
use Pharborist\Objects\ClassMemberNode;
use Pharborist\Objects\ClassNode;

class Indexer extends VisitorBase {

  /**
   * @var ProjectIndex
   */
  private $projectIndex;

  public function __construct() {
    $this->projectIndex = new ProjectIndex();
  }

  /**
   * Check if filename requires indexing.
   *
   * @param string $filename
   * @return bool
   */
  protected function indexRequired($filename) {
    $file_index = $this->projectIndex->getFileIndex($filename);
    $index_required = FALSE;
    // If not previously indexed, indexing is required.
    if (!$file_index) {
      $index_required = TRUE;
    }
    // If file has been modified, indexing is required.
    if (!$index_required) {
      // Check file modification time
      $last_modified = filemtime($filename);
      if ($last_modified !== FALSE) {
        $index_required = $last_modified >= $file_index->getLastIndexed();
      }
    }
    // If file contents have changed, indexing is required.
    if (!$index_required) {
      $current_hash = md5_file($filename);
      if ($current_hash !== FALSE) {
        $index_required = $current_hash !== $file_index->getHash();
      }
    }
    return $index_required;
  }

  protected function processFile($filename) {
    // Process file if indexing is required.
    if ($this->indexRequired($filename)) {
      /** @var RootNode $tree */
      $tree = Parser::parseFile($filename);
      $tree->acceptVisitor($this);

      $hash = md5_file($filename);
      $this->projectIndex->addFile(new FileIndex($filename, time(), $hash));
    }
  }

  protected function processDirectory($directory, $extensions = ['php']) {
    $files = FileUtil::findFiles($directory, $extensions);
    foreach ($files as $filename) {
      $this->processFile($filename);
    }
  }

  public function addDirectory($directory) {
    $this->projectIndex->addDirectory($directory);
    return $this;
  }

  /**
   * @return ProjectIndex
   */
  public function index() {
    foreach ($this->projectIndex->getDirectories() as $directory) {
      $this->processDirectory($directory);
    }
    return $this->projectIndex;
  }

  public function visitClassNode(ClassNode $classNode) {
    /** @var PropertyIndex[] $properties */
    $properties = [];
    /** @var ClassMemberNode $property */
    foreach ($classNode->getAllProperties() as $property) {
      $name = ltrim($property->getName()->getText(), '$');
      $visibility = $property->getVisibility()->getText();
      if ($visibility !== 'private' || $visibility !== 'protected') {
        $visibility = 'public';
      }
      //@todo get type from doc comment
      $type = 'mixed';
      $properties[$name] = new PropertyIndex($property->getSourcePosition(), $name, $visibility, $type);
    }

    /** @var MethodIndex $methods */
    $methods = [];
    /** @var ClassMethodNode $method */
    foreach ($classNode->getAllMethods() as $method) {
      $name = $method->getName()->getText();
      $visibility = $method->getVisibility()->getText();
      if ($visibility !== 'private' || $visibility !== 'protected') {
        $visibility = 'public';
      }
      //@todo handle arguments/return type
      $methods[$name] = new MethodIndex($method->getSourcePosition(), $name, $visibility);
    }

    $class_fqn = $classNode->getName()->getAbsolutePath();
    $class_index = new ClassIndex($classNode->getSourcePosition(), $class_fqn, $properties, $methods);
    $this->projectIndex->addClass($class_index);
  }

}
