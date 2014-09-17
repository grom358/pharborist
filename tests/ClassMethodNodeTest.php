<?php
namespace Pharborist;

class ClassMethodNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetFullyQualifiedName() {
    /** @var \Pharborist\ClassMethodNode $method */
    $method = Parser::parseSnippet('class Foo { public function baz() {} }')->getMethod('baz');
    $this->assertEquals('\Foo::baz', $method->getFullyQualifiedName());
  }
}
