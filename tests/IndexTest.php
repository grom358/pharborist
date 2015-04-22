<?php
namespace Pharborist;

use Pharborist\Index\Indexer;
use Pharborist\Index\ProjectIndex;

class IndexTest extends \PHPUnit_Framework_TestCase {
  protected $index;

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

  public function setUp() {
    // @TODO Build $this->index
    // Knights\Lancelot is a class
    // Knight is an interface
  }

  /**
   * ProjectIndex::getFiles() should return a collection of FileIndex, keyed by
   * file name.
   */
  public function testGetFiles() {
    $files = $this->index->getFiles();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $files);

    $keys = [
      'Knight.php',
      'Camelot.php',
      'Lancelot.php',
      'Galahad.php',
      'Robin.php',
      'Tim.php',
      'BraveTrait.php',
      'PureTrait.php',
      'CowardTrait.php',
    ];
    $this->assertEquals($keys, $files->getKeys());

    foreach ($keys as $key) {
      $this->assertInstanceOf('\Pharborist\Index\FileIndex', $files[$key]);
    }
    $this->assertInstanceOf('\Pharborist\Index\FileIndex', $this->index->getFile('Knight.php'));
  }

  /**
   * ProjectIndex::getClasses() should return a collection of ClassIndex,
   * keyed by fully-qualified name.
   */
  public function testGetClasses() {
    $classes = $this->index->getClasses();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $classes);
    $this->assertTrue($classes->containsKey('Knights\Lancelot'));
    $this->assertInstanceOf('\Pharborist\Index\ClassIndex', $classes['Knights\Lancelot']);
    $this->assertInstanceOf('\Pharborist\Index\ClassIndex', $this->index->getClass('Knights\Lancelot'));
  }

  /**
   * ProjectIndex::getInterfaces() should return a collection of InterfaceIndex,
   * keyed by fully-qualified name.
   */
  public function testGetInterfaces() {
    $interfaces = $this->index->getClasses();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $interfaces);
    $this->assertTrue($interfaces->containsKey('Knight'));
    $this->assertInstanceOf('\Pharborist\Index\InterfaceIndex', $interfaces['Knight']);
    $this->assertInstanceOf('\Pharborist\Index\InterfaceIndex', $this->index->getInterface('Knight'));
  }

  /**
   * ProjectIndex::getTraits() should return a collection of TraitIndex,
   * keyed by fully-qualified name.
   */
  public function testGetTraits() {
    $traits = $this->index->getTraits();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $traits);

    $keys = [
      'BraveTrait',
      'PureTrait',
      'CowardTrait',
    ];
    $this->assertEquals($keys, $traits->getKeys());

    foreach ($keys as $key) {
      $this->assertInstanceOf('\Pharborist\Index\TraitIndex', $traits[$key]);
    }
    $this->assertInstanceOf('\Pharborist\Index\TraitIndex', $this->index->getTrait('BraveTrait'));
  }

  /**
   * ProjectIndex::getFunctions() should return a collection of FunctionIndex
   * keyed by name.
   */
  public function testGetFunctions() {
    $functions = $this->index->getFunctions();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $functions);

    $keys = [
      'create_fireball',
      'be_menacing',
      'be_scottish',
      'set_rabbit_warning',
    ];
    $this->assertEquals($keys, $functions->getKeys());

    foreach ($keys as $key) {
      $this->assertInstanceOf('\Pharborist\Index\FunctionIndex', $functions[$key]);
    }
    $this->assertInstanceOf('\Pharborist\Index\TraitIndex', $this->index->getFunction('create_fireball'));
  }

  /**
   * ProjectIndex::getConstants() should return a collection of ConstantIndex
   * keyed by fully-qualified name.
   */
  public function testGetConstants() {
    $constants = $this->index->getConstants();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $constants);

    $keys = [
      'TIM_ACCENT',
      'Knights\Lancelot::GENDER',
      'Knights\Galahad::GENDER',
      'Knights\Robin::GENDER',
    ];
    $this->assertEquals($keys, $constants->getKeys());

    foreach ($keys as $key) {
      $this->assertInstanceOf('\Pharborist\Index\ConstantIndex', $constants[$key]);
    }
    $this->assertInstanceOf('\Pharborist\Index\ConstantIndex', $this->index->getConstant('TIM_ACCENT'));
  }

  /**
   * ClassIndex::getProperties() should return a collection of PropertyIndex,
   * keyed by name.
   */
  public function testGetClassProperties() {
    $properties = $this->index->getClass('Knights\Lancelot')->getProperties();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $properties);

    $keys = [
      'sword',
      'isBerserk',
    ];
    $this->assertEquals($keys, $properties->getKeys());

    foreach ($keys as $key) {
      $this->assertInstanceOf('\Pharborist\Index\PropertyIndex', $properties[$key]);
    }
    $this->assertInstanceOf('\Pharborist\Index\PropertyIndex', $properties->get('sword'));
  }

  /**
   * ClassIndex::getOwnProperties() should return a collection of PropertyIndex,
   * keyed by name, which are NOT inherited from parent classes.
   */
  public function testGetClassOwnProperties() {
    $properties = $this->index->getClass('Knights\Lancelot')->getOwnProperties();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $properties);
    $this->assertEquals([ 'isBerserk' ], $properties->getKeys());
    $this->assertInstanceOf('\Pharborist\Index\PropertyIndex', $properties['isBerserk']);
  }

  /**
   * ClassIndex::getMethods() should return a collection of MethodIndex, keyed
   * by name.
   */
  public function testGetClassMethods() {
    $methods = $this->index->getClass('Knights\Lancelot')->getMethods();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $methods);

    $keys = [
      'attack',
      'retreat',
      'runStrangely',
    ];
    $this->assertEquals($keys, $methods->getKeys());

    foreach ($keys as $key) {
      $this->assertInstanceOf('\Pharborist\Index\MethodIndex', $methods[$key]);
    }
    $this->assertInstanceOf('\Pharborist\Index\PropertyIndex', $methods->get('attack'));
  }

  /**
   * ClassIndex::getOwnMethods() should return a collection of MethodIndex,
   * keyed by name, which are NOT inherited from parent classes.
   */
  public function testGetClassOwnMethods() {
    $methods = $this->index->getClass('Knights\Lancelot')->getOwnMethods();
    $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $methods);
    $this->assertEquals([ 'runStrangely' ], $methods->getKeys());
    $this->assertInstanceOf('\Pharborist\Index\PropertyIndex', $methods['runStrangely']);
  }

  public function testGetClassConstants() {
    // $index->getClass('foo')->getConstants() returns a collection of
    // ConstantIndex keyed by name
  }

  public function testGetClassOwnConstants() {
    // $index->getClass('foo')->getOwnConstants() returns a collection of
    // ConstantIndex keyed by name
  }

  public function testGetInterfaceMethods() {
    // $index->getInterface('foo')->getMethods() returns a collection of
    // MethodIndex keyed by name
  }

  public function testGetInterfaceOwnMethods() {
    // $index->getInterface('foo')->getOwnMethods() returns a collection of
    // MethodIndex keyed by name
  }

  public function testGetInterfaceConstants() {
    // $index->getInterface('foo')->getConstants() returns a collection of
    // ConstantIndex keyed by name
  }

  public function testGetInterfaceOwnConstants() {
    // $index->getInterface('foo')->getOwnConstants() returns a collection of
    // ConstantIndex keyed by name
  }

  public function testGetTraitProperties() {
    // $index->getTrait('foo')->getProperties() returns a collection of
    // PropertyIndex keyed by name
  }

  public function testGetTraitOwnProperties() {
    // $index->getTrait('foo')->getOwnProperties() returns a collection of
    // PropertyIndex keyed by name
  }

  public function testGetTraitMethods() {
    // $index->getTrait('foo')->getMethods() returns a collection of
    // MethodIndex keyed by name
  }

  public function testGetTraitOwnMethods() {
    // $index->getTrait('foo')->getOwnMethods() returns a collection of
    // MethodIndex keyed by name
  }

  public function testGetTraitConstants() {
    // $index->getTrait('foo')->getConstants() returns a collection of
    // ConstantIndex keyed by name
  }

  public function testGetTraitOwnConstants() {
    // $index->getTrait('foo')->getOwnConstants() returns a collection of
    // ConstantIndex keyed by name
  }

  public function testGetClassParent() {
    // $index->getClass('foo')->getParent() returns a ClassIndex or NULL
  }

  public function testGetClassInterfaces() {
    // $index->getClass('foo')->getInterfaces() returns a collection of
    // InterfaceIndex keyed by fully qualified name
  }

  public function testGetClassTraits() {
    // $index->getClass('foo')->getTraits() returns a collection of TraitIndex
    // keyed by fully qualified name
  }

  public function testGetClassExtendedBy() {
    // $index->getClass('foo')->extendedBy() returns a collection of
    // ClassIndex keyed by fully qualified name
  }

  public function testGetInterfaceParents() {
    // $index->getInterface('foo')->getParents() returns a collection of
    // InterfaceIndex keyed by fully qualified name
  }

  public function testGetInterfaceExtendedBy() {
    // $index->getInterface('foo')->extendedBy() returns a collection of
    // InterfaceIndex keyed by fully qualified name
  }

  public function testGetInterfaceImplementedBy() {
    // $index->getInterface('foo')->implementedBy() returns a collection of
    // ClassIndex keyed by fully qualified name
  }

  public function testGetTraitClasses() {
    // $index->getTrait('foo')->getClasses() returns a collection of
    // ClassIndex keyed by fully qualified name
  }

  public function testGetTraitTraitsUsing() {
    // $index->getTrait('foo')->getTraitsUsing() returns a collection of
    // TraitIndex keyed by fully qualified name
  }

  public function testGetTraitTraitsUsed() {
    // $index->getTrait('foo')->getTraitsUsed() returns a collection of
    // TraitIndex keyed by fully qualified name
  }

  public function testGetClassInstances() {
    // $index->getClass('foo')->getInstances() returns SourcePosition[]
  }

  public function testGetFunctionCalls() {
    // $index->getFunction('foo')->getCalls() returns SourcePosition[]
  }

  public function testFileAdd() {
    // $index->addFile('foo') should add classes, functions, constants,
    // interfaces, and traits it contains
  }

  public function testFileUpdate() {
    // $index->getFile('foo')->update() should re-index the file (call
    // delete(), then add())
  }

  public function testFileDelete() {
    // $index->getFile('foo')->delete() should delete classes, functions,
    // constants, interfaces, and traits it contains
  }

  public function testMethodAnnotation() {
    // $index->getClass('foo')->getMethod('baz') should return a MethodIndex
    // for @method mixed baz(integer $bar)
  }

  public function testPropertyAnnotation() {
    // $index->getClass('foo')->getProperty('baz') should return a
    // PropertyIndex for @property string $baz
  }

  public function testGetNamespaces() {
    // $index->getNamespaces() should return a collection of NamespaceIndex
    // keyed by fully-qualified name
  }

  public function testGetClassesInNamespace() {
    // $index->getNamespace('foo')->getClasses() should return a collection
    // of ClassIndex keyed by fully-qualified name
  }

  public function testGetInterfacesInNamespace() {
    // $index->getNamespace('foo')->getInterfaces() should return a collection
    // of InterfaceIndex keyed by fully-qualified name
  }

  public function testGetTraitsInNamespace() {
    // $index->getNamespace('foo')->getTraits() should return a collection
    // of TraitIndex keyed by fully-qualified name
  }

  public function testGetFunctionsInNamespace() {
    // $index->getNamespace('foo')->getFunctions() should return a collection
    // of FunctionIndex keyed by fully-qualified name
  }

  public function testGetConstantsInNamespace() {
    // $index->getNamespace('foo')->getConstants() should return a collection
    // of ConstantIndex keyed by fully-qualified name
  }
}
