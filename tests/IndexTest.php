<?php
namespace Pharborist;

use Pharborist\Index\Indexer;
use Pharborist\Index\ProjectIndex;

class IndexTest extends \PHPUnit_Framework_TestCase {
  public function testIndex() {
    chdir(dirname(__FILE__));
    $indexer = new Indexer();
    $indexer->addDirectory('index_test');
    $index = $indexer->index();
    $classes = $index->getClasses();
    $this->assertArrayHasKey('\MyNamespace\MyClass', $classes);
    $class_index = $classes['\MyNamespace\MyClass'];
    $this->assertFalse($class_index->isFinal());
    $this->assertFalse($class_index->isAbstract());
    $properties = $class_index->getProperties();
    $this->assertArrayHasKey('myProperty', $properties);
    $property = $properties['myProperty'];
    $expected = ['\MyNamespace\SomeType', '\MyNamespace\Relative\TestType'];
    $this->assertEquals($expected, $property->getTypes());
    $methods = $class_index->getMethods();
    $this->assertArrayHasKey('myMethod', $methods);
    $method = $methods['myMethod'];
    $this->assertEquals(['int'], $method->getReturnTypes());
    $parameters = $method->getParameters();
    $this->assertArrayHasKey('arg', $parameters);
    $parameter = $parameters['arg'];
    $this->assertEquals(['string'], $parameter->getTypes());

    $indexer = new Indexer();
    // Remove source directory from index, to test for removal of file.
    $index = new ProjectIndex([], $index->getFiles(), $index->getClasses());
    $indexer->load($index);
    $index = $indexer->index();
    $this->assertEmpty($index->getFiles());
    $this->assertEmpty($index->getClasses());
  }
}
