<?php
namespace Pharborist;

class InterfaceNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetMethodNames() {
    /** @var \Pharborist\Objects\InterfaceNode $interface */
    $interface = Parser::parseSnippet('interface Foo { public function wambooli(); }');
    $this->assertSame(['wambooli'], $interface->getMethodNames());
  }

  public function testHasMethod() {
    /** @var \Pharborist\Objects\InterfaceNode $interface */
    $interface = Parser::parseSnippet('interface Foo { public function wambooli(); }');
    $this->assertTrue($interface->hasMethod('wambooli'));
    $this->assertFalse($interface->hasMethod('blorf'));
  }

  public function testGetMethods() {
    /** @var \Pharborist\Objects\InterfaceNode $interface */
    $interface = Parser::parseSnippet('interface Foo { public function wambooli(); }');
    $methods = $interface->getMethods();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $methods);
    $this->assertEquals(1, $methods->count());
  }

  public function testGetMethod() {
    /** @var \Pharborist\Objects\InterfaceNode $interface */
    $interface = Parser::parseSnippet('interface Foo { public function wambooli(); }');
    $method = $interface->getMethod('wambooli');
    $this->assertInstanceOf('\Pharborist\Objects\InterfaceMethodNode', $method);
    $this->assertEquals('wambooli', $method->getName()->getText());
    $this->assertNull($interface->getMethod('harrr'));
  }

  public function testGetConstants() {
    /** @var \Pharborist\Objects\InterfaceNode $interface */
    $interface = Parser::parseSnippet('interface Foo { const ANSWER = 42; }');
    $constants = $interface->getConstants();
    $this->assertCount(1, $constants);
    /** @var \Pharborist\Constants\ConstantDeclarationNode $constant */
    $constant = $constants[0];
    $this->assertInstanceOf('\Pharborist\Constants\ConstantDeclarationNode', $constant);
    $this->assertEquals('ANSWER', $constant->getName());
    $this->assertEquals('42', $constant->getValue()->getText());
  }
}
