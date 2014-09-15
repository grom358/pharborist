<?php
namespace Pharborist;

class ClassNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetPropertyNames() {
    $class = Parser::parseSnippet('class Foo { protected $bar; public $baz; }');
    $this->assertSame(['bar', 'baz'], $class->getPropertyNames());
  }
}
