<?php
namespace Pharborist;

use Pharborist\Index\Indexer;

class IndexTest extends \PHPUnit_Framework_TestCase {
  public function testIndex() {
    chdir(dirname(__FILE__));
    $indexer = new Indexer();
    $indexer->addDirectory('index_test');
    $index = $indexer->index();
    $classes = $index->getClasses();
    $this->assertArrayHasKey('\MyNamespace\MyClass', $classes);
    $class_index = $classes['\MyNamespace\MyClass'];
    $properties = $class_index->getProperties();
    $this->assertArrayHasKey('myProperty', $properties);
    $methods = $class_index->getMethods();
    $this->assertArrayHasKey('myMethod', $methods);
  }
}
