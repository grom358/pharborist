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
}
