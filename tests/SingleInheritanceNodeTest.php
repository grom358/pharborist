<?php
namespace Pharborist;

class SingleInheritanceNodeTest extends \PHPUnit_Framework_TestCase {
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

  public function testGetProperties() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { protected $bar; public $baz; }');
    $properties = $class->getProperties();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $properties);
    $this->assertEquals(2, $properties->count());
  }

  public function testGetMethods() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { public function wambooli() {} }');
    $methods = $class->getMethods();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $methods);
    $this->assertEquals(1, $methods->count());
  }

  public function testGetConstants() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet("class Foo { const WAMBOOLI = 'blorf'; }");
    $constants = $class->getConstants();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $constants);
    $this->assertEquals(1, $constants->count());
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

  public function testGetTraitUses() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { use Bar; use Soap; }');
    $traits = $class->getTraitUses();
    $this->assertCount(2, $traits);
    $this->assertInstanceOf('\Pharborist\Objects\TraitUseNode', $traits[0]);
    $this->assertInstanceOf('\Pharborist\Objects\TraitUseNode', $traits[1]);
  }

  public function testGetTraits() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { use Bar; }');
    $traits = $class->getTraits();
    $this->assertCount(1, $traits);
    $trait = $traits[0];
    $this->assertInstanceOf('\Pharborist\Namespaces\NameNode', $trait);
    $this->assertEquals('Bar', $trait->getPath());
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

  public function testSetImplementsStringArray() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo {}');
    $class->setImplements(['\IteratorAggregate', '\ArrayAccess']);
    $implements = $class->getImplementList();
    $this->assertInstanceOf('\Pharborist\CommaListNode', $implements);
    $items = $implements->getItems();
    $this->assertCount(2, $items);
    $this->assertEquals('\IteratorAggregate, \ArrayAccess', $implements->getText());
  }
}
