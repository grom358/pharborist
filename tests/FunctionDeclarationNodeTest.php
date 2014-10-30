<?php

namespace Pharborist;

class FunctionDeclarationNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCloneAsMethodOf() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Camelot {}');
    /** @var \Pharborist\Functions\FunctionDeclarationNode $func */
    $func = Parser::parseSnippet('function sing_goofy_song() {}');

    $func->cloneAsMethodOf($class);
    $this->assertTrue($class->hasMethod('sing_goofy_song'));
  }

  public function testCreateFilter() {
    $func = Parser::parseSnippet('function foo() {}');
    $this->assertInstanceOf('\Pharborist\Filters\FunctionDeclarationFilter', $func->filter());
  }
}
