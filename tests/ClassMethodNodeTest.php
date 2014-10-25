<?php
namespace Pharborist;

class ClassMethodNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetFullyQualifiedName() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Foo { public function baz() {} }');
    /** @var \Pharborist\Objects\ClassMethodNode $method */
    $method = $class->getMethod('baz');
    $this->assertEquals('\Foo::baz', $method->getFullyQualifiedName());
  }
}
