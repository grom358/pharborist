<?php
namespace Pharborist;

class ClassNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetPropertyNames() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { protected $bar; public $baz; }');
    $this->assertSame(['bar', 'baz'], $class->getPropertyNames());
  }

  public function testGetMethodNames() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { public function wambooli() {} }');
    $this->assertSame(['wambooli'], $class->getMethodNames());
  }

  public function testHasProperty() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { protected $bar; }');
    $this->assertTrue($class->hasProperty('bar'));
    $this->assertTrue($class->hasProperty('$bar'));
    $this->assertFalse($class->hasProperty('baz'));
    $this->assertFalse($class->hasProperty('$baz'));
  }

  public function testHasMethod() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { public function wambooli() {} }');
    $this->assertTrue($class->hasMethod('wambooli'));
    $this->assertFalse($class->hasMethod('blorf'));
  }

  public function testGetAllProperties() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { protected $bar; public $baz; }');
    $properties = $class->getAllProperties();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $properties);
    $this->assertEquals(2, $properties->count());
  }

  public function testGetAllMethods() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { public function wambooli() {} }');
    $methods = $class->getAllMethods();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $methods);
    $this->assertEquals(1, $methods->count());
  }

  public function testGetProperty() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { protected $bar; public $baz; }');
    $property = $class->getProperty('baz');
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberNode', $property);
    $this->assertEquals('$baz', $property->getText());
    $this->assertNull($class->getProperty('oops'));
  }

  public function testGetMethod() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { public function wambooli() {} }');
    $method = $class->getMethod('wambooli');
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodNode', $method);
    $this->assertEquals('wambooli', $method->getName()->getText());
    $this->assertNull($class->getMethod('harrr'));
  }

  /**
   * @depends testHasProperty
   */
  public function testCreateProperty() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo {}');
    $class->createProperty('baz', NULL, 'protected');
    $this->assertTrue($class->hasProperty('baz'));
  }
}
