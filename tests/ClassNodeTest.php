<?php
namespace Pharborist;

class ClassNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetPropertyNames() {
    $class = Parser::parseSnippet('class Foo { protected $bar; public $baz; }');
    $this->assertSame(['bar', 'baz'], $class->getPropertyNames());
  }

  public function testGetMethodNames() {
    $class = Parser::parseSnippet('class Foo { public function wambooli() {} }');
    $this->assertSame(['wambooli'], $class->getMethodNames());
  }

  public function testHasProperty() {
    $class = Parser::parseSnippet('class Foo { protected $bar; }');
    $this->assertTrue($class->hasProperty('bar'));
    $this->assertTrue($class->hasProperty('$bar'));
    $this->assertFalse($class->hasProperty('baz'));
    $this->assertFalse($class->hasProperty('$baz'));
  }

  public function testHasMethod() {
    $class = Parser::parseSnippet('class Foo { public function wambooli() {} }');
    $this->assertTrue($class->hasMethod('wambooli'));
    $this->assertFalse($class->hasMethod('blorf'));
  }

  public function testGetAllProperties() {
    $properties = Parser::parseSnippet('class Foo { protected $bar; public $baz; }')->getAllProperties();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $properties);
    $this->assertEquals(2, $properties->count());
  }

  public function testGetAllMethods() {
    $methods = Parser::parseSnippet('class Foo { public function wambooli() {} }')->getAllMethods();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $methods);
    $this->assertEquals(1, $methods->count());
  }

  public function testGetProperty() {
    $class = Parser::parseSnippet('class Foo { protected $bar; public $baz; }');
    $property = $class->getProperty('baz');
    $this->assertInstanceOf('\Pharborist\ClassMemberNode', $property);
    $this->assertEquals('$baz', $property->getText());
    $this->assertNull($class->getProperty('oops'));
  }

  public function testGetMethod() {
    $class = Parser::parseSnippet('class Foo { public function wambooli() {} }');
    $method = $class->getMethod('wambooli');
    $this->assertInstanceOf('\Pharborist\ClassMethodNode', $method);
    $this->assertEquals('wambooli', $method->getName()->getText());
    $this->assertNull($class->getMethod('harrr'));
  }
}
