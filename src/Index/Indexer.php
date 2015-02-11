<?php
namespace Pharborist\Index;

use Pharborist\Parser;
use Pharborist\RootNode;
use Pharborist\VisitorBase;
use Pharborist\Objects\ClassMemberNode;
use Pharborist\Objects\ClassNode;

class Indexer extends VisitorBase {

  /**
   * @var ClassIndex[]
   */
  private $classes = [];

  public function processFile($filename) {
    /** @var RootNode $tree */
    $tree = Parser::parseFile($filename);
    $tree->acceptVisitor($this);
  }

  public function visitClassNode(ClassNode $classNode) {
    /** @var PropertyIndex[] $properties */
    $properties = [];
    /** @var ClassMemberNode $property */
    foreach ($classNode->getAllProperties() as $property) {
      $name = $property->getName()->getText();
      $visibility = $property->getVisibility()->getText();
      if ($visibility !== 'private' || $visibility !== 'protected') {
        $visibility = 'public';
      }
      //@todo get type from doc comment
      $type = 'mixed';
      $properties[] = new PropertyIndex($property->getSourcePosition(), $name, $visibility, $type);
    }

    /** @var MethodIndex $methods */
    $methods = [];

    $class_fqn = $classNode->getName()->getAbsolutePath();
    $this->classes[] = new ClassIndex($classNode->getSourcePosition(), $class_fqn, $properties, $methods);
  }

}
