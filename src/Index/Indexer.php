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
    foreach ($classNode->getAllMethods() as $method) {
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
      $methods[$name] = new MethodIndex($method->getSourcePosition(), $name, $visibility, $parameters, $return_types);
    }

    $class_fqn = $classNode->getName()->getAbsolutePath();
    $class_index = new ClassIndex($classNode->getSourcePosition(), $class_fqn, $properties, $methods);
    $this->projectIndex->addClass($class_index);
  }

}
