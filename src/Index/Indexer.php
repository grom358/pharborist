<?php
namespace Pharborist\Index;

use Pharborist\FileUtil;
use Pharborist\Objects\ClassMethodNode;
use Pharborist\Parser;
use Pharborist\RootNode;
use Pharborist\VisitorBase;
use Pharborist\Objects\ClassMemberNode;
use Pharborist\Objects\ClassNode;
use phpDocumentor\Reflection\DocBlock;

class Indexer extends VisitorBase {

  /**
   * @var string[]
   */
  private $directories;

  /**
   * @var FileIndex[]
   */
  private $files;

  /**
   * @var ClassIndex[]
   */
  private $classes;

  /**
   * Classes defined by the current file being processed.
   *
   * @var string[]
   */
  private $fileClasses;

  /**
   * List of files that no longer exist in project.
   *
   * @var string[]
   */
  private $deletedFiles;

  public function __construct() {
    $this->directories = [];
    $this->files = [];
    $this->classes = [];
  }

  /**
   * Check if filename requires indexing.
   *
   * @param string $filename
   * @return bool
   */
  protected function indexRequired($filename) {
    if (!isset($this->files[$filename])) {
      return TRUE;
    }
    $file_index = $this->files[$filename];
    // If file has been modified, indexing is required.
    $last_modified = filemtime($filename);
    if ($last_modified === FALSE || $last_modified >= $file_index->getLastIndexed()) {
      return TRUE;
    }
    // If file contents have changed, indexing is required.
    $current_hash = md5_file($filename);
    if ($current_hash === FALSE || $current_hash !== $file_index->getHash()) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Remove all definitions and usages that belong to a file.
   *
   * @param FileIndex $fileIndex
   */
  protected function fileRemoved(FileIndex $fileIndex) {
    foreach ($fileIndex->getClasses() as $class_fqn) {
      unset($this->classes[$class_fqn]);
    }
    unset($this->files[$fileIndex->getFilename()]);
  }

  protected function processFile($filename) {
    // Process file if indexing is required.
    if ($this->indexRequired($filename)) {
      if (isset($this->files[$filename])) {
        $this->fileRemoved($this->files[$filename]);
      }

      $this->fileClasses = [];

      /** @var RootNode $tree */
      $tree = Parser::parseFile($filename);
      $tree->acceptVisitor($this);

      $hash = md5_file($filename);
      $this->files[$filename] = new FileIndex($filename, time(), $hash, $this->fileClasses);
    }
  }

  protected function processDirectory($directory, $extensions = ['php']) {
    $files = FileUtil::findFiles($directory, $extensions);
    foreach ($files as $filename) {
      $this->processFile($filename);
      unset($this->deletedFiles[$filename]);
    }
  }

  public function addDirectory($directory) {
    $this->directories[] = $directory;
    return $this;
  }

  /**
   * Load an existing index into the indexer.
   *
   * @param ProjectIndex $index
   *   Project index to load.
   *
   * @return $this
   */
  public function load(ProjectIndex $index) {
    $this->directories = $index->getDirectories();
    $this->files = $index->getFiles();
    $this->classes = $index->getClasses();
  }

  /**
   * @return ProjectIndex
   */
  public function index() {
    $this->deletedFiles = array_flip(array_keys($this->files));
    // Scan directories for files.
    foreach ($this->directories as $directory) {
      $this->processDirectory($directory);
    }
    // Handle files that no longer exist.
    foreach ($this->deletedFiles as $filename => $dummy) {
      $file_index = $this->files[$filename];
      $this->fileRemoved($file_index);
    }
    return new ProjectIndex(
      $this->directories,
      $this->files,
      $this->classes
    );
  }

  public function visitClassNode(ClassNode $classNode) {
    /** @var PropertyIndex[] $properties */
    $properties = [];
    /** @var ClassMemberNode $property */
    foreach ($classNode->getProperties() as $property) {
      $name = ltrim($property->getName()->getText(), '$');
      $visibility = $property->getVisibility()->getText();
      if ($visibility !== 'private' || $visibility !== 'protected') {
        $visibility = 'public';
      }
      $types = ['mixed'];
      $doc_comment = $property->getClassMemberListNode()->getDocComment();
      if ($doc_comment) {
        $var_tags = $doc_comment->getDocBlock()->getTagsByName('var');
        if (!empty($var_tags)) {
          /** @var DocBlock\Tag\VarTag $var_tag */
          $var_tag = end($var_tags);
          $types = $var_tag->getTypes();
        }
      }
      $properties[$name] = new PropertyIndex($property->getSourcePosition(), $name, $visibility, $types);
    }

    /** @var MethodIndex[] $methods */
    $methods = [];
    /** @var ClassMethodNode $method */
    foreach ($classNode->getMethods() as $method) {
      $name = $method->getName()->getText();
      $visibility = $method->getVisibility()->getText();
      if ($visibility !== 'private' || $visibility !== 'protected') {
        $visibility = 'public';
      }
      $return_types = [];
      $doc_comment = $method->getDocComment();
      $parameter_tags = [];
      if ($doc_comment) {
        $param_tags = $doc_comment->getDocBlock()->getTagsByName('param');
        /** @var DocBlock\Tag\ParamTag $param_tag */
        foreach ($param_tags as $param_tag) {
          $variable_name = ltrim($param_tag->getVariableName(), '$');
          if ($variable_name) {
            $parameter_tags[$variable_name] = $param_tag;
          }
        }
        $return_tags = $doc_comment->getDocBlock()->getTagsByName('return');
        if (!empty($return_tags)) {
          /** @var DocBlock\Tag\ReturnTag $return_tag */
          $return_tag = end($return_tags);
          $return_types = $return_tag->getTypes();
        }
      }
      $parameters = [];
      $i = 0;
      foreach ($method->getParameters() as $parameter_node) {
        $param_name = $parameter_node->getName();
        $parameter_types = ['mixed'];
        $param_tag = isset($parameter_tags[$param_name]) ? $parameter_tags[$param_name] : NULL;
        if ($param_tag) {
          $parameter_types = $param_tag->getTypes();
        }
        $type_hint = $parameter_node->getTypeHint();
        if ($type_hint) {
          $parameter_types = [$type_hint];
        }
        $parameter = new ParameterIndex(
          $parameter_node->getSourcePosition(),
          $param_name,
          $parameter_types
        );
        // Store by both index and name.
        $parameters[$i++] = $parameters[$param_name] = $parameter;
      }
      $final = $method->getFinal() !== NULL;
      $static = $method->getStatic() !== NULL;
      $abstract = $method->getAbstract() !== NULL;
      $methods[$name] = new MethodIndex($method->getSourcePosition(), $name, $visibility, $final, $static, $abstract, $parameters, $return_types);
    }

    $class_fqn = $classNode->getName()->getAbsolutePath();
    $final = $classNode->getFinal() !== NULL;
    $abstract = $classNode->getAbstract() !== NULL;
    $class_index = new ClassIndex($classNode->getSourcePosition(), $class_fqn, $final, $abstract, $properties, $methods);
    $this->classes[$class_fqn] = $class_index;
    $this->fileClasses[] = $class_fqn;
  }

}
