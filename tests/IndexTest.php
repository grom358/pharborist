<?php
namespace Pharborist;

use Pharborist\Index\Indexer;
use Pharborist\Index\ProjectIndex;

/**
 * Test the indexer.
 */
class IndexTest extends \PHPUnit_Framework_TestCase {

  public function testExample() {
    $baseDir = dirname(__FILE__) . '/index_tests/example';

    // Remove index from filesystem.
    @unlink($baseDir . '/.pharborist');

    // Create index.
    $indexer = new Indexer($baseDir);
    $indexer->getFileSet()->addDirectory('src');
    $index = $indexer->index();

    $this->assertTrue($index->classExists('\Example\Person'));
    $this->assertTrue($index->classExists('\Example\Communicator'));
    $this->assertTrue($index->interfaceExists('\Example\Speaker'));
    $this->assertTrue($index->traitExists('\Example\Ni'));
    $this->assertTrue($index->constantExists('\Example\ULTIMATE_ANSWER'));
    $this->assertTrue($index->functionExists('\Example\ask'));

    $class = $index->getClass('\Example\Person');
    $this->assertEquals(['\Example\Communicator'], $class->getExtendedBy());
    $methods = $class->getMethods();
    $this->assertArrayHasKey('__construct', $methods);
    $method = $methods['__construct'];
    $parameters = $method->getParameters();
    $this->assertCount(1, $parameters);
    $parameter = $parameters[0];
    $this->assertEquals('name', $parameter->getName());
    $this->assertEquals(['string'], $parameter->getTypes());
    $this->assertEquals(['void'], $method->getReturnTypes());
    $this->assertArrayHasKey('getName', $methods);
    $method = $methods['getName'];
    $this->assertEquals(['string'], $method->getReturnTypes());
    $properties = $class->getProperties();
    $this->assertArrayHasKey('name', $properties);
    $this->assertEquals(['string'], $properties['name']->getTypes());

    $interface = $index->getInterface('\Example\Speaker');
    $constants = $interface->getConstants();
    $this->assertArrayHasKey('HELLO', $constants);
    $methods = $interface->getMethods();
    $this->assertArrayHasKey('speak', $methods);
    $this->assertEquals(['void'], $methods['speak']->getReturnTypes());

    $function = $index->getFunction('\Example\ask');
    $this->assertEquals(['string'], $function->getReturnTypes());
    $parameters = $function->getParameters();
    $this->assertCount(1, $parameters);
    $parameter = $parameters[0];
    $this->assertEquals('question', $parameter->getName());
    $this->assertEquals(['string'], $parameter->getTypes());

    $class = $index->getClass('\Example\Communicator');
    $this->assertEquals('\Example\Person', $class->getExtends());
    $this->assertEquals(['\Example\PublicSpeaker'], $class->getImplements());
    $this->assertEquals(['\Example\Ni'], $class->getTraits());
    $this->assertTrue($class->hasMethod('getName'));
    $this->assertEquals('\Example\Person', $class->getMethod('getName')->getOwner());
    $this->assertTrue($class->hasMethod('speak'));
    $this->assertEquals('\Example\Communicator', $class->getMethod('speak')->getOwner());
    $this->assertTrue($class->hasMethod('ni'));
    $this->assertEquals('\Example\Ni', $class->getMethod('ni')->getOwner());
    $constants = $class->getConstants();
    $this->assertArrayHasKey('HELLO', $constants);
  }

  public function testTraits() {
    $baseDir = dirname(__FILE__) . '/index_tests/traits';

    // Remove index from filesystem.
    @unlink($baseDir . '/.pharborist');

    // Create index.
    $indexer = new Indexer($baseDir);
    $indexer->getFileSet()->addDirectory('src');
    $index = $indexer->index();

    $this->assertTrue($index->classExists('\Example\Base'));
    $this->assertTrue($index->traitExists('\Example\SayWorld'));
    $this->assertTrue($index->classExists('\Example\MyHelloWorld'));
    $class = $index->getClass('\Example\MyHelloWorld');
    $this->assertTrue($class->hasMethod('sayHello'));
    $method = $class->getMethod('sayHello');
    $this->assertEquals('\Example\SayWorld', $method->getOwner());

    $this->assertTrue($index->traitExists('\Example\HelloWorld'));
    $this->assertTrue($index->classExists('\Example\TheWorldIsNotEnough'));
    $class = $index->getClass('\Example\TheWorldIsNotEnough');
    $this->assertTrue($class->hasMethod('sayHello'));
    $method = $class->getMethod('sayHello');
    $this->assertEquals('\Example\TheWorldIsNotEnough', $method->getOwner());

    $this->assertTrue($index->traitExists('\Example\A'));
    $this->assertTrue($index->traitExists('\Example\B'));
    $this->assertTrue($index->classExists('\Example\Talker'));

    $class = $index->getClass('\Example\Talker');
    $methods = $class->getMethods();
    $this->assertArrayHasKey('smallTalk', $methods);
    $this->assertArrayHasKey('bigTalk', $methods);
    $this->assertArrayHasKey('talk', $methods);
    $this->assertEquals('private', $methods['talk']->getVisibility());

    $this->assertTrue($index->classExists('\Example\Person'));
    $this->assertTrue($index->traitExists('\Example\Ni'));
    $this->assertTrue($index->classExists('\Example\KingArthur'));
    $class = $index->getClass('\Example\KingArthur');
    $methods = $class->getMethods();
    $this->assertArrayHasKey('getName', $methods);
    $this->assertEquals('\Example\Person', $methods['getName']->getOwner());
    $this->assertArrayHasKey('ni', $methods);
    $this->assertEquals('\Example\Ni', $methods['ni']->getOwner());
    $this->assertArrayHasKey('sayHello', $methods);
    $this->assertEquals('\Example\KingArthur', $methods['sayHello']->getOwner());
    $this->assertArrayHasKey('quote', $methods);
    $this->assertEquals('\Example\KingArthur', $methods['quote']->getOwner());

    // Load index from filesystem and check against the saved index.
    $loadedIndex = ProjectIndex::load($baseDir);
    $this->assertEquals($index, $loadedIndex);
  }

  public function testErrors() {
    $baseDir = dirname(__FILE__) . '/index_tests/errors';

    // Remove index from filesystem.
    @unlink($baseDir . '/.pharborist');

    // Create index.
    $indexer = new Indexer($baseDir);
    $indexer->getFileSet()->addDirectory('src');
    $index = $indexer->index();

    $this->assertEquals([
      'Trait alias conflictMethod at src/Conflict.php:31 conflicts with existing alias at src/Conflict.php:30',
      'Trait precedence at src/Conflict.php:39 conflicts with existing rule at src/Conflict.php:38',
      'Required trait \Example\C wasn\'t added in trait \Example\MissingRequiredTrait at src/Conflict.php:43',
      'Class \Example\Missing at src/Missing.php:4 extends missing class \Example\MissingClass',
      'Class \Example\Missing at src/Missing.php:4 implements missing interface \Example\MissingInterface',
      'Class \Example\Missing at src/Missing.php:4 uses missing trait \Example\MissingTrait',
      'Trait \Example\T at src/Missing.php:8 uses missing trait \Example\MissingTrait',
      'Interface \Example\I at src/Missing.php:12 extends missing interface \Example\MissingInterface'
    ], $index->getErrors());

    // Load index from filesystem and check against the saved index.
    $loadedIndex = ProjectIndex::load($baseDir);
    $this->assertEquals($index, $loadedIndex);
  }

}
